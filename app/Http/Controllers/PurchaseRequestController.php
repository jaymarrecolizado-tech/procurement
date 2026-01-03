<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\PrItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\DocumentGeneratorService;

class PurchaseRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = PurchaseRequest::with(['endUser', 'prItems']);

        // Filter based on user role
        if ($user->hasRole('END_USER')) {
            $query->where('end_user_id', $user->id);
        }

        // Search filter
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('pr_number', 'like', '%' . $request->search . '%')
                  ->orWhere('project_title', 'like', '%' . $request->search . '%')
                  ->orWhere('project_description', 'like', '%' . $request->search . '%');
            });
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $purchaseRequests = $query->latest()->paginate(15);

        return view('purchase-requests.index', compact('purchaseRequests'));
    }

    public function create()
    {
        if (!Auth::user()->hasAnyRole(['END_USER', 'PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }
        return view('purchase-requests.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasAnyRole(['END_USER', 'PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'project_title' => 'required|string|max:255',
            'project_description' => 'nullable|string',
            'end_user_department' => 'required|string|max:255',
            'fund_source' => 'required|string|max:255',
            'estimated_budget' => 'required|numeric|min:0',
            'urgency_level' => 'required|in:LOW,MEDIUM,HIGH,URGENT',
            'urgency_timeline' => 'nullable|string|max:255',
            'approval_date' => 'required|date',
            'has_signatures' => 'boolean',
            'has_specs' => 'boolean',
            'has_quantity' => 'boolean',
            'has_market_survey' => 'boolean',
            'deficiency_notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_code' => 'required|string|max:255',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_of_measure' => 'required|string|max:50',
            'items.*.estimated_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $pr = PurchaseRequest::create([
                'pr_number' => PurchaseRequest::generatePrNumber(),
                'project_title' => $validated['project_title'],
                'project_description' => $validated['project_description'] ?? null,
                'end_user_id' => Auth::id(),
                'end_user_department' => $validated['end_user_department'],
                'fund_source' => $validated['fund_source'],
                'estimated_budget' => $validated['estimated_budget'],
                'urgency_level' => $validated['urgency_level'],
                'urgency_timeline' => $validated['urgency_timeline'] ?? null,
                'approval_date' => $validated['approval_date'],
                'status' => 'PR_UNDER_REVIEW',
                'has_signatures' => $request->has('has_signatures'),
                'has_specs' => $request->has('has_specs'),
                'has_quantity' => $request->has('has_quantity'),
                'has_market_survey' => $request->has('has_market_survey'),
                'deficiency_notes' => $validated['deficiency_notes'] ?? null,
                'purpose' => $validated['purpose'] ?? null,
                'requested_by_name' => $validated['requested_by_name'] ?? null,
                'requested_by_designation' => $validated['requested_by_designation'] ?? null,
                'approved_by_name' => $validated['approved_by_name'] ?? null,
                'approved_by_designation' => $validated['approved_by_designation'] ?? null,
                'budget_officer_name' => $validated['budget_officer_name'] ?? null,
                'budget_officer_designation' => $validated['budget_officer_designation'] ?? null,
                'office_address' => $validated['office_address'] ?? null,
                'office_name' => $validated['office_name'] ?? null,
                'responsibility_center' => $validated['responsibility_center'] ?? null,
            ]);

            // Create PR items
            foreach ($validated['items'] as $item) {
                PrItem::create([
                    'purchase_request_id' => $pr->id,
                    'item_code' => $item['item_code'],
                    'item_name' => $item['item_name'],
                    'item_description' => $item['item_description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_of_measure' => $item['unit_of_measure'],
                    'estimated_price' => $item['estimated_price'],
                    'total_estimated' => $item['quantity'] * $item['estimated_price'],
                ]);
            }

            activity_log('CREATED', 'PURCHASE_REQUEST', $pr->id, [
                'pr_number' => $pr->pr_number,
                'project_title' => $pr->project_title,
            ]);

            DB::commit();

            return redirect()->route('purchase-requests.show', $pr)
                ->with('success', 'Purchase Request created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create purchase request: ' . $e->getMessage()]);
        }
    }

    public function show(PurchaseRequest $purchaseRequest)
    {
        $user = Auth::user();
        if ($user->hasRole('END_USER') && $purchaseRequest->end_user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        // Load relationships - use loadMissing to avoid errors if RFQ doesn't exist
        $purchaseRequest->loadMissing(['endUser', 'prItems', 'rfq']);
        return view('purchase-requests.show', compact('purchaseRequest'));
    }

    public function edit(PurchaseRequest $purchaseRequest)
    {
        $user = Auth::user();
        if ($user->hasRole('END_USER') && $purchaseRequest->end_user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        if (!Auth::user()->hasAnyRole(['END_USER', 'PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }
        
        $purchaseRequest->load('prItems');
        return view('purchase-requests.edit', compact('purchaseRequest'));
    }

    public function update(Request $request, PurchaseRequest $purchaseRequest)
    {
        $user = Auth::user();
        if ($user->hasRole('END_USER') && $purchaseRequest->end_user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        if (!Auth::user()->hasAnyRole(['END_USER', 'PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'project_title' => 'required|string|max:255',
            'project_description' => 'nullable|string',
            'end_user_department' => 'required|string|max:255',
            'fund_source' => 'required|string|max:255',
            'estimated_budget' => 'required|numeric|min:0',
            'urgency_level' => 'required|in:LOW,MEDIUM,HIGH,URGENT',
            'urgency_timeline' => 'nullable|string|max:255',
            'approval_date' => 'required|date',
            'has_signatures' => 'boolean',
            'has_specs' => 'boolean',
            'has_quantity' => 'boolean',
            'has_market_survey' => 'boolean',
            'deficiency_notes' => 'nullable|string',
            'purpose' => 'nullable|string',
            'requested_by_name' => 'nullable|string|max:255',
            'requested_by_designation' => 'nullable|string|max:255',
            'approved_by_name' => 'nullable|string|max:255',
            'approved_by_designation' => 'nullable|string|max:255',
            'budget_officer_name' => 'nullable|string|max:255',
            'budget_officer_designation' => 'nullable|string|max:255',
            'office_address' => 'nullable|string|max:255',
            'office_name' => 'nullable|string|max:255',
            'responsibility_center' => 'nullable|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.item_code' => 'required|string|max:255',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_of_measure' => 'required|string|max:50',
            'items.*.estimated_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $purchaseRequest->update([
                'project_title' => $validated['project_title'],
                'project_description' => $validated['project_description'] ?? null,
                'end_user_department' => $validated['end_user_department'],
                'fund_source' => $validated['fund_source'],
                'estimated_budget' => $validated['estimated_budget'],
                'urgency_level' => $validated['urgency_level'],
                'urgency_timeline' => $validated['urgency_timeline'] ?? null,
                'approval_date' => $validated['approval_date'],
                'has_signatures' => $request->has('has_signatures'),
                'has_specs' => $request->has('has_specs'),
                'has_quantity' => $request->has('has_quantity'),
                'has_market_survey' => $request->has('has_market_survey'),
                'deficiency_notes' => $validated['deficiency_notes'] ?? null,
                'purpose' => $validated['purpose'] ?? null,
                'requested_by_name' => $validated['requested_by_name'] ?? null,
                'requested_by_designation' => $validated['requested_by_designation'] ?? null,
                'approved_by_name' => $validated['approved_by_name'] ?? null,
                'approved_by_designation' => $validated['approved_by_designation'] ?? null,
                'budget_officer_name' => $validated['budget_officer_name'] ?? null,
                'budget_officer_designation' => $validated['budget_officer_designation'] ?? null,
                'office_address' => $validated['office_address'] ?? null,
                'office_name' => $validated['office_name'] ?? null,
                'responsibility_center' => $validated['responsibility_center'] ?? null,
            ]);

            // Delete existing items and create new ones
            $purchaseRequest->prItems()->delete();
            foreach ($validated['items'] as $item) {
                PrItem::create([
                    'purchase_request_id' => $purchaseRequest->id,
                    'item_code' => $item['item_code'],
                    'item_name' => $item['item_name'],
                    'item_description' => $item['item_description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_of_measure' => $item['unit_of_measure'],
                    'estimated_price' => $item['estimated_price'],
                    'total_estimated' => $item['quantity'] * $item['estimated_price'],
                ]);
            }

            activity_log('UPDATED', 'PURCHASE_REQUEST', $purchaseRequest->id, [
                'pr_number' => $purchaseRequest->pr_number,
            ]);

            DB::commit();

            return redirect()->route('purchase-requests.show', $purchaseRequest)
                ->with('success', 'Purchase Request updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update purchase request: ' . $e->getMessage()]);
        }
    }

    public function destroy(PurchaseRequest $purchaseRequest)
    {
        $user = Auth::user();
        if ($user->hasRole('END_USER') && $purchaseRequest->end_user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        if (!Auth::user()->hasAnyRole(['END_USER', 'PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        activity_log('DELETED', 'PURCHASE_REQUEST', $purchaseRequest->id, [
            'pr_number' => $purchaseRequest->pr_number,
        ]);

        $purchaseRequest->delete();

        return redirect()->route('purchase-requests.index')
            ->with('success', 'Purchase Request deleted successfully.');
    }

    public function updateStatus(Request $request, PurchaseRequest $purchaseRequest)
    {
        if (!Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => 'required|in:PR_UNDER_REVIEW,RFQ_READY,RFQ_DISSEMINATED,CANVASS_COMPLETE,BAC_DOCS_READY,BAC_APPROVED,PO_APPROVED,AWAITING_CONFORME,PO_COMPLETE,COA_STAMPED',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $purchaseRequest->status;
        $purchaseRequest->update(['status' => $validated['status']]);

        activity_log('STATUS_UPDATED', 'PURCHASE_REQUEST', $purchaseRequest->id, [
            'old_status' => $oldStatus,
            'new_status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return back()->with('success', 'Status updated successfully.');
    }

    public function generatePDF(PurchaseRequest $purchaseRequest)
    {
        $user = Auth::user();
        if ($user->hasRole('END_USER') && $purchaseRequest->end_user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $documentGenerator = new DocumentGeneratorService();
        return $documentGenerator->downloadPurchaseRequestPDF($purchaseRequest);
    }

    public function previewPDF(PurchaseRequest $purchaseRequest)
    {
        $user = Auth::user();
        if ($user->hasRole('END_USER') && $purchaseRequest->end_user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $documentGenerator = new DocumentGeneratorService();
        return $documentGenerator->streamPurchaseRequestPDF($purchaseRequest);
    }
}

