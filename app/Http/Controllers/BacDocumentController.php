<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\BacDocument;
use App\Models\SupplierQuotation;
use App\Models\QuotationItem;
use App\Models\ApprovalRouting;
use App\Models\User;
use App\Notifications\ApprovalRequired;
use App\Notifications\ItemApproved;
use App\Notifications\ItemRejected;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BacDocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseRequest::with(['endUser', 'bacDocuments'])
            ->whereIn('status', ['CANVASS_COMPLETE', 'BAC_DOCS_READY', 'BAC_APPROVED', 'PO_APPROVED']);

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('pr_number', 'like', '%' . $request->search . '%')
                  ->orWhere('project_title', 'like', '%' . $request->search . '%');
            });
        }

        $purchaseRequests = $query->latest()->paginate(15);

        return view('bac-documents.index', compact('purchaseRequests'));
    }

    public function create(PurchaseRequest $purchaseRequest)
    {
        if (!Auth::user()->hasAnyRole(['BAC_SECRETARIAT', 'PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        // Check if PR has completed canvassing
        if ($purchaseRequest->status !== 'CANVASS_COMPLETE' && $purchaseRequest->status !== 'BAC_DOCS_READY') {
            return redirect()->route('purchase-requests.show', $purchaseRequest)
                ->with('error', 'Purchase Request must have completed canvassing before creating BAC documents.');
        }

        $purchaseRequest->load(['prItems', 'rfq.canvasses.supplierQuotations.quotationItems']);

        // Get all supplier quotations for this PR
        $supplierQuotations = SupplierQuotation::whereHas('canvass.rfq', function($query) use ($purchaseRequest) {
            $query->where('purchase_request_id', $purchaseRequest->id);
        })->with(['canvass.rfq', 'quotationItems.prItem'])->get();

        // Get existing BAC documents
        $existingDocuments = $purchaseRequest->bacDocuments()->pluck('document_type')->toArray();

        return view('bac-documents.create', compact('purchaseRequest', 'supplierQuotations', 'existingDocuments'));
    }

    public function store(Request $request, PurchaseRequest $purchaseRequest)
    {
        if (!Auth::user()->hasAnyRole(['BAC_SECRETARIAT', 'PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'document_type' => 'required|in:ABSTRACT_OF_QUOTATIONS,PRICE_MATRIX,TWG_CERT,RECOMMENDATION,RESOLUTION',
            'procurement_mode' => 'nullable|in:SHOPPING,SVP,PUBLIC_BIDDING,NEGOTIATED,DIRECT_CONTRACTING',
            'content' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // Check if document type already exists
            $existing = $purchaseRequest->bacDocuments()
                ->where('document_type', $validated['document_type'])
                ->first();

            if ($existing) {
                return back()->withInput()->withErrors(['document_type' => 'This document type already exists for this Purchase Request.']);
            }

            // Auto-generate content for Abstract of Quotations and Price Matrix
            $content = $validated['content'] ?? [];
            
            if ($validated['document_type'] === 'ABSTRACT_OF_QUOTATIONS') {
                $content = $this->generateAbstractOfQuotations($purchaseRequest);
            } elseif ($validated['document_type'] === 'PRICE_MATRIX') {
                $content = $this->generatePriceMatrix($purchaseRequest);
            }

            $bacDocument = BacDocument::create([
                'purchase_request_id' => $purchaseRequest->id,
                'document_type' => $validated['document_type'],
                'procurement_mode' => $validated['procurement_mode'] ?? null,
                'content' => $content,
                'status' => 'DRAFT',
            ]);

            // Update PR status if first BAC document
            if ($purchaseRequest->status === 'CANVASS_COMPLETE') {
                $purchaseRequest->update(['status' => 'BAC_DOCS_READY']);
            }

            activity_log('CREATED', 'BAC_DOCUMENT', $bacDocument->id, [
                'document_type' => $bacDocument->document_type,
                'purchase_request_id' => $purchaseRequest->id,
            ]);

            DB::commit();

            return redirect()->route('bac-documents.show', $bacDocument)
                ->with('success', 'BAC document created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create BAC document: ' . $e->getMessage()]);
        }
    }

    public function show(BacDocument $bacDocument)
    {
        $bacDocument->loadMissing(['purchaseRequest.prItems', 'purchaseRequest.endUser', 'approvalRoutings.approver']);
        
        // Get supplier quotations for context
        $supplierQuotations = SupplierQuotation::whereHas('canvass.rfq', function($query) use ($bacDocument) {
            $query->where('purchase_request_id', $bacDocument->purchase_request_id);
        })->with(['canvass.rfq', 'quotationItems.prItem'])->get();

        return view('bac-documents.show', compact('bacDocument', 'supplierQuotations'));
    }

    public function edit(BacDocument $bacDocument)
    {
        if (!Auth::user()->hasAnyRole(['BAC_SECRETARIAT', 'PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($bacDocument->status === 'APPROVED') {
            return redirect()->route('bac-documents.show', $bacDocument)
                ->with('error', 'Cannot edit an approved document.');
        }

        $bacDocument->load(['purchaseRequest.prItems', 'purchaseRequest.rfq.canvasses.supplierQuotations.quotationItems']);

        return view('bac-documents.edit', compact('bacDocument'));
    }

    public function update(Request $request, BacDocument $bacDocument)
    {
        if (!Auth::user()->hasAnyRole(['BAC_SECRETARIAT', 'PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($bacDocument->status === 'APPROVED') {
            return redirect()->route('bac-documents.show', $bacDocument)
                ->with('error', 'Cannot update an approved document.');
        }

        $validated = $request->validate([
            'procurement_mode' => 'nullable|in:SHOPPING,SVP,PUBLIC_BIDDING,NEGOTIATED,DIRECT_CONTRACTING',
            'content' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            // Regenerate content for Abstract and Price Matrix if requested
            if ($request->has('regenerate') && in_array($bacDocument->document_type, ['ABSTRACT_OF_QUOTATIONS', 'PRICE_MATRIX'])) {
                if ($bacDocument->document_type === 'ABSTRACT_OF_QUOTATIONS') {
                    $validated['content'] = $this->generateAbstractOfQuotations($bacDocument->purchaseRequest);
                } elseif ($bacDocument->document_type === 'PRICE_MATRIX') {
                    $validated['content'] = $this->generatePriceMatrix($bacDocument->purchaseRequest);
                }
            }

            $bacDocument->update([
                'procurement_mode' => $validated['procurement_mode'] ?? $bacDocument->procurement_mode,
                'content' => $validated['content'] ?? $bacDocument->content,
            ]);

            activity_log('UPDATED', 'BAC_DOCUMENT', $bacDocument->id, [
                'document_type' => $bacDocument->document_type,
            ]);

            DB::commit();

            return redirect()->route('bac-documents.show', $bacDocument)
                ->with('success', 'BAC document updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update BAC document: ' . $e->getMessage()]);
        }
    }

    public function destroy(BacDocument $bacDocument)
    {
        if (!Auth::user()->hasAnyRole(['BAC_SECRETARIAT', 'PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($bacDocument->status === 'APPROVED') {
            return redirect()->route('bac-documents.show', $bacDocument)
                ->with('error', 'Cannot delete an approved document.');
        }

        DB::beginTransaction();
        try {
            $prId = $bacDocument->purchase_request_id;
            $documentType = $bacDocument->document_type;

            activity_log('DELETED', 'BAC_DOCUMENT', $bacDocument->id, [
                'document_type' => $documentType,
            ]);

            $bacDocument->delete();

            // Check if PR should revert status
            $remainingDocs = BacDocument::where('purchase_request_id', $prId)->count();
            if ($remainingDocs === 0) {
                $purchaseRequest = PurchaseRequest::find($prId);
                if ($purchaseRequest && $purchaseRequest->status === 'BAC_DOCS_READY') {
                    $purchaseRequest->update(['status' => 'CANVASS_COMPLETE']);
                }
            }

            DB::commit();

            return redirect()->route('purchase-requests.show', $prId)
                ->with('success', 'BAC document deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete BAC document: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate Abstract of Quotations content from supplier quotations
     */
    private function generateAbstractOfQuotations(PurchaseRequest $purchaseRequest): array
    {
        $supplierQuotations = SupplierQuotation::whereHas('canvass.rfq', function($query) use ($purchaseRequest) {
            $query->where('purchase_request_id', $purchaseRequest->id);
        })->with(['quotationItems.prItem', 'canvass'])->get();

        $abstract = [
            'pr_number' => $purchaseRequest->pr_number,
            'project_title' => $purchaseRequest->project_title,
            'date' => now()->format('Y-m-d'),
            'suppliers' => [],
        ];

        foreach ($supplierQuotations as $quotation) {
            $abstract['suppliers'][] = [
                'supplier_name' => $quotation->supplier_name,
                'supplier_address' => $quotation->supplier_address,
                'supplier_contact' => $quotation->supplier_contact,
                'supplier_email' => $quotation->supplier_email,
                'quote_price' => $quotation->quote_price,
                'delivery_days' => $quotation->delivery_days,
                'is_compliant' => $quotation->is_compliant,
                'is_selected' => $quotation->is_selected,
                'submitted_date' => $quotation->submitted_date->format('Y-m-d'),
                'remarks' => $quotation->remarks,
            ];
        }

        return $abstract;
    }

    /**
     * Generate Price Matrix content comparing all supplier quotations
     */
    private function generatePriceMatrix(PurchaseRequest $purchaseRequest): array
    {
        $prItems = $purchaseRequest->prItems;
        $supplierQuotations = SupplierQuotation::whereHas('canvass.rfq', function($query) use ($purchaseRequest) {
            $query->where('purchase_request_id', $purchaseRequest->id);
        })->with(['quotationItems.prItem'])->get();

        $matrix = [
            'pr_number' => $purchaseRequest->pr_number,
            'project_title' => $purchaseRequest->project_title,
            'date' => now()->format('Y-m-d'),
            'items' => [],
            'suppliers' => [],
            'totals' => [],
        ];

        // Build supplier list
        foreach ($supplierQuotations as $quotation) {
            $matrix['suppliers'][] = [
                'id' => $quotation->id,
                'name' => $quotation->supplier_name,
                'total' => $quotation->quote_price,
                'is_selected' => $quotation->is_selected,
            ];
        }

        // Build item comparison matrix
        foreach ($prItems as $prItem) {
            $itemData = [
                'item_code' => $prItem->item_code,
                'item_name' => $prItem->item_name,
                'quantity' => $prItem->quantity,
                'unit_of_measure' => $prItem->unit_of_measure,
                'prices' => [],
            ];

            foreach ($supplierQuotations as $quotation) {
                $quotationItem = $quotation->quotationItems()
                    ->where('pr_item_id', $prItem->id)
                    ->first();

                $itemData['prices'][] = [
                    'supplier_id' => $quotation->id,
                    'unit_price' => $quotationItem ? $quotationItem->unit_price : null,
                    'total_price' => $quotationItem ? $quotationItem->total_price : null,
                ];
            }

            $matrix['items'][] = $itemData;
        }

        // Calculate totals per supplier
        foreach ($supplierQuotations as $quotation) {
            $matrix['totals'][] = [
                'supplier_id' => $quotation->id,
                'total' => $quotation->quote_price,
            ];
        }

        return $matrix;
    }

    /**
     * Show form to assign approvers to BAC document
     */
    public function assignApprovers(BacDocument $bacDocument)
    {
        if (!Auth::user()->hasAnyRole(['BAC_SECRETARIAT', 'PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        if ($bacDocument->status === 'APPROVED') {
            return redirect()->route('bac-documents.show', $bacDocument)
                ->with('error', 'Cannot assign approvers to an approved document.');
        }

        // Get users with BAC roles
        $bacMembers = User::whereIn('role', ['BAC_MEMBER', 'BAC_CHAIR'])->get();
        $existingRoutings = $bacDocument->approvalRoutings()->with('approver')->orderBy('sequence')->get();

        return view('bac-documents.assign-approvers', compact('bacDocument', 'bacMembers', 'existingRoutings'));
    }

    /**
     * Store approval routing assignments
     */
    public function storeApprovers(Request $request, BacDocument $bacDocument)
    {
        if (!Auth::user()->hasAnyRole(['BAC_SECRETARIAT', 'PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'approvers' => 'required|array|min:1',
            'approvers.*.approver_id' => 'required|exists:users,id',
            'approvers.*.sequence' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            // Delete existing routings
            $bacDocument->approvalRoutings()->delete();

            // Create new routings
            foreach ($validated['approvers'] as $approverData) {
                $approver = User::find($approverData['approver_id']);
                
                $routing = ApprovalRouting::create([
                    'bac_document_id' => $bacDocument->id,
                    'document_type' => 'BAC',
                    'approver_id' => $approverData['approver_id'],
                    'approver_role' => $approver->role,
                    'sequence' => $approverData['sequence'],
                    'status' => 'PENDING',
                ]);

                // Send notification to first approver (sequence 1)
                if ($approverData['sequence'] == 1) {
                    $approver->notify(new ApprovalRequired(
                        'BAC',
                        $bacDocument->id,
                        $bacDocument->purchaseRequest->pr_number,
                        $bacDocument->purchaseRequest->project_title,
                        $approverData['sequence'],
                        route('bac-documents.show', $bacDocument)
                    ));
                }
            }

            // Update document status to PENDING_APPROVAL
            $bacDocument->update(['status' => 'PENDING_APPROVAL']);

            activity_log('ROUTED', 'BAC_DOCUMENT', $bacDocument->id, [
                'document_type' => $bacDocument->document_type,
                'approvers_count' => count($validated['approvers']),
            ]);

            DB::commit();

            return redirect()->route('bac-documents.show', $bacDocument)
                ->with('success', 'Approvers assigned successfully. Document is now pending approval.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to assign approvers: ' . $e->getMessage()]);
        }
    }

    /**
     * Approve BAC document
     */
    public function approve(Request $request, BacDocument $bacDocument)
    {
        $user = Auth::user();
        
        // Check if user is an approver for this document
        $routing = $bacDocument->approvalRoutings()
            ->where('approver_id', $user->id)
            ->where('status', 'PENDING')
            ->orderBy('sequence')
            ->first();

        if (!$routing) {
            return back()->withErrors(['error' => 'You are not authorized to approve this document or it is not pending your approval.']);
        }

        // Check if previous approvals are complete (sequential approval)
        $previousRoutings = $bacDocument->approvalRoutings()
            ->where('sequence', '<', $routing->sequence)
            ->where('status', '!=', 'APPROVED')
            ->exists();

        if ($previousRoutings) {
            return back()->withErrors(['error' => 'Previous approvers must approve before you can approve.']);
        }

        $validated = $request->validate([
            'comments' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $startTime = $routing->created_at;
            $timeSpent = now()->diffInHours($startTime);

            $routing->update([
                'status' => 'APPROVED',
                'signed_at' => now(),
                'comments' => $validated['comments'] ?? null,
                'time_spent_hours' => $timeSpent,
            ]);

            // Check if all approvals are complete
            $allApproved = $bacDocument->approvalRoutings()
                ->where('status', '!=', 'APPROVED')
                ->doesntExist();

            if ($allApproved) {
                $bacDocument->update(['status' => 'APPROVED']);
                
                // Update PR status to BAC_APPROVED
                $bacDocument->purchaseRequest->update(['status' => 'BAC_APPROVED']);

                // Notify PR creator and procurement officer
                $pr = $bacDocument->purchaseRequest;
                if ($pr->endUser) {
                    $pr->endUser->notify(new ItemApproved(
                        'BAC',
                        $bacDocument->id,
                        $pr->pr_number,
                        $pr->project_title,
                        $user->name,
                        route('bac-documents.show', $bacDocument)
                    ));
                }

                activity_log('APPROVED', 'BAC_DOCUMENT', $bacDocument->id, [
                    'document_type' => $bacDocument->document_type,
                    'approved_by' => $user->id,
                    'all_approvals_complete' => true,
                ]);
            } else {
                // Get next approver and notify them
                $nextRouting = $bacDocument->approvalRoutings()
                    ->where('sequence', '>', $routing->sequence)
                    ->where('status', 'PENDING')
                    ->orderBy('sequence')
                    ->first();

                if ($nextRouting && $nextRouting->approver) {
                    $nextRouting->approver->notify(new ApprovalRequired(
                        'BAC',
                        $bacDocument->id,
                        $bacDocument->purchaseRequest->pr_number,
                        $bacDocument->purchaseRequest->project_title,
                        $nextRouting->sequence,
                        route('bac-documents.show', $bacDocument)
                    ));
                }

                activity_log('APPROVED', 'BAC_DOCUMENT', $bacDocument->id, [
                    'document_type' => $bacDocument->document_type,
                    'approved_by' => $user->id,
                    'next_approver' => $nextRouting ? $nextRouting->approver_id : null,
                ]);
            }

            DB::commit();

            return back()->with('success', 'Document approved successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to approve document: ' . $e->getMessage()]);
        }
    }

    /**
     * Reject BAC document
     */
    public function reject(Request $request, BacDocument $bacDocument)
    {
        $user = Auth::user();
        
        // Check if user is an approver for this document
        $routing = $bacDocument->approvalRoutings()
            ->where('approver_id', $user->id)
            ->where('status', 'PENDING')
            ->first();

        if (!$routing) {
            return back()->withErrors(['error' => 'You are not authorized to reject this document or it is not pending your approval.']);
        }

        $validated = $request->validate([
            'comments' => 'required|string|min:10',
        ], [
            'comments.required' => 'Please provide a reason for rejection.',
            'comments.min' => 'Rejection reason must be at least 10 characters.',
        ]);

        DB::beginTransaction();
        try {
            $routing->update([
                'status' => 'REJECTED',
                'signed_at' => now(),
                'comments' => $validated['comments'],
            ]);

            // Reject document
            $bacDocument->update(['status' => 'REJECTED']);

            // Notify PR creator
            $pr = $bacDocument->purchaseRequest;
            if ($pr->endUser) {
                $pr->endUser->notify(new ItemRejected(
                    'BAC',
                    $bacDocument->id,
                    $pr->pr_number,
                    $pr->project_title,
                    $user->name,
                    $validated['comments'],
                    route('bac-documents.show', $bacDocument)
                ));
            }

            activity_log('REJECTED', 'BAC_DOCUMENT', $bacDocument->id, [
                'document_type' => $bacDocument->document_type,
                'rejected_by' => $user->id,
                'reason' => $validated['comments'],
            ]);

            DB::commit();

            return redirect()->route('bac-documents.show', $bacDocument)
                ->with('success', 'Document rejected. The document creator will be notified.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to reject document: ' . $e->getMessage()]);
        }
    }

    /**
     * Get pending approvals for current user
     */
    public function pendingApprovals(Request $request)
    {
        $user = Auth::user();
        
        $query = ApprovalRouting::where('approver_id', $user->id)
            ->where('status', 'PENDING')
            ->with(['bacDocument.purchaseRequest.endUser', 'bacDocument.approvalRoutings.approver'])
            ->orderBy('sequence');

        $pendingApprovals = $query->paginate(15);

        return view('bac-documents.pending-approvals', compact('pendingApprovals'));
    }
}

