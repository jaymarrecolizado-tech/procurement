<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PurchaseRequest;
use App\Models\PurchaseOrder;
use App\Models\SupplierQuotation;
use App\Models\RFQ;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = PurchaseOrder::with(['purchaseRequest.endUser', 'supplier']);

        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('po_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('purchaseRequest', function($prQuery) use ($request) {
                      $prQuery->where('pr_number', 'like', '%' . $request->search . '%')
                             ->orWhere('project_title', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        $purchaseOrders = $query->latest()->paginate(15);

        return view('purchase-orders.index', compact('purchaseOrders'));
    }

    public function create(PurchaseRequest $purchaseRequest)
    {
        if (!Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        // Check if PR is BAC approved
        if ($purchaseRequest->status !== 'BAC_APPROVED') {
            return redirect()->route('purchase-requests.show', $purchaseRequest)
                ->with('error', 'Purchase Request must be BAC approved before creating a Purchase Order.');
        }

        // Check if PO already exists
        if ($purchaseRequest->purchaseOrder) {
            return redirect()->route('purchase-orders.show', $purchaseRequest->purchaseOrder)
                ->with('info', 'Purchase Order already exists for this Purchase Request.');
        }

        // Find selected supplier quotation
        $selectedQuotation = $this->getSelectedSupplierQuotation($purchaseRequest);

        if (!$selectedQuotation) {
            return redirect()->route('purchase-requests.show', $purchaseRequest)
                ->with('error', 'No selected supplier quotation found. Please select a winning quotation first.');
        }

        $purchaseRequest->load(['prItems', 'rfq', 'endUser']);

        return view('purchase-orders.create', compact('purchaseRequest', 'selectedQuotation'));
    }

    public function store(Request $request, PurchaseRequest $purchaseRequest)
    {
        if (!Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        // Find selected supplier quotation
        $selectedQuotation = $this->getSelectedSupplierQuotation($purchaseRequest);

        if (!$selectedQuotation) {
            return back()->withInput()->withErrors(['error' => 'No selected supplier quotation found.']);
        }

        $validated = $request->validate([
            'delivery_instructions' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'delivery_deadline' => 'required|date|after:today',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            // Calculate delivery deadline from quotation if not provided
            $deliveryDeadline = $validated['delivery_deadline'] ?? now()->addDays($selectedQuotation->delivery_days ?? 30);

            $purchaseOrder = PurchaseOrder::create([
                'po_number' => PurchaseOrder::generatePoNumber(),
                'purchase_request_id' => $purchaseRequest->id,
                'supplier_id' => null, // Supplier may not be a user in the system
                'supplier_name' => $selectedQuotation->supplier_name,
                'supplier_address' => $selectedQuotation->supplier_address,
                'supplier_contact' => $selectedQuotation->supplier_contact,
                'contract_amount' => $selectedQuotation->quote_price,
                'delivery_instructions' => $validated['delivery_instructions'] ?? $purchaseRequest->rfq->delivery_schedule ?? null,
                'payment_terms' => $validated['payment_terms'] ?? $purchaseRequest->rfq->payment_terms ?? null,
                'delivery_deadline' => $deliveryDeadline,
                'status' => 'DRAFT',
                'notes' => $validated['notes'] ?? null,
            ]);

            // Update PR status
            $purchaseRequest->update(['status' => 'PO_APPROVED']);

            activity_log('CREATED', 'PURCHASE_ORDER', $purchaseOrder->id, [
                'po_number' => $purchaseOrder->po_number,
                'purchase_request_id' => $purchaseRequest->id,
                'supplier_name' => $purchaseOrder->supplier_name,
            ]);

            DB::commit();

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase Order created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create Purchase Order: ' . $e->getMessage()]);
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['purchaseRequest.prItems', 'purchaseRequest.endUser', 'purchaseRequest.rfq', 'supplier', 'documents.uploader']);

        // Get selected quotation for reference
        $selectedQuotation = $this->getSelectedSupplierQuotation($purchaseOrder->purchaseRequest);

        return view('purchase-orders.show', compact('purchaseOrder', 'selectedQuotation'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if (!Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        if (in_array($purchaseOrder->status, ['DISSEMINATED', 'AWAITING_CONFORME', 'COMPLETE'])) {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Cannot edit a Purchase Order that has been disseminated.');
        }

        $purchaseOrder->load(['purchaseRequest.prItems', 'purchaseRequest.rfq']);

        return view('purchase-orders.edit', compact('purchaseOrder'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        if (in_array($purchaseOrder->status, ['DISSEMINATED', 'AWAITING_CONFORME', 'COMPLETE'])) {
            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('error', 'Cannot update a Purchase Order that has been disseminated.');
        }

        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_address' => 'nullable|string',
            'supplier_contact' => 'nullable|string|max:255',
            'contract_amount' => 'required|numeric|min:0',
            'delivery_instructions' => 'nullable|string',
            'payment_terms' => 'nullable|string',
            'delivery_deadline' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $purchaseOrder->update($validated);

            activity_log('UPDATED', 'PURCHASE_ORDER', $purchaseOrder->id, [
                'po_number' => $purchaseOrder->po_number,
            ]);

            DB::commit();

            return redirect()->route('purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase Order updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update Purchase Order: ' . $e->getMessage()]);
        }
    }

    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        if (!Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => 'required|in:DRAFT,PENDING_APPROVAL,APPROVED,DISSEMINATED,AWAITING_CONFORME,COMPLETE',
        ]);

        DB::beginTransaction();
        try {
            $oldStatus = $purchaseOrder->status;
            $purchaseOrder->update(['status' => $validated['status']]);

            // Update PR status based on PO status
            if ($validated['status'] === 'DISSEMINATED') {
                $purchaseOrder->purchaseRequest->update(['status' => 'AWAITING_CONFORME']);
            } elseif ($validated['status'] === 'COMPLETE') {
                $purchaseOrder->purchaseRequest->update(['status' => 'PO_COMPLETE']);
            }

            activity_log('STATUS_UPDATED', 'PURCHASE_ORDER', $purchaseOrder->id, [
                'po_number' => $purchaseOrder->po_number,
                'old_status' => $oldStatus,
                'new_status' => $validated['status'],
            ]);

            DB::commit();

            return back()->with('success', 'Purchase Order status updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update status: ' . $e->getMessage()]);
        }
    }

    public function generatePDF(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['purchaseRequest.prItems', 'purchaseRequest.endUser']);
        
        $documentGenerator = app(\App\Services\DocumentGeneratorService::class);
        return $documentGenerator->downloadPurchaseOrderPDF($purchaseOrder);
    }

    public function previewPDF(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['purchaseRequest.prItems', 'purchaseRequest.endUser']);
        
        $documentGenerator = app(\App\Services\DocumentGeneratorService::class);
        return $documentGenerator->streamPurchaseOrderPDF($purchaseOrder);
    }

    /**
     * Get the selected supplier quotation for a purchase request
     */
    private function getSelectedSupplierQuotation(PurchaseRequest $purchaseRequest): ?SupplierQuotation
    {
        return SupplierQuotation::whereHas('canvass.rfq', function($query) use ($purchaseRequest) {
            $query->where('purchase_request_id', $purchaseRequest->id);
        })
        ->where('is_selected', true)
        ->with(['quotationItems.prItem', 'canvass.rfq'])
        ->first();
    }
}

