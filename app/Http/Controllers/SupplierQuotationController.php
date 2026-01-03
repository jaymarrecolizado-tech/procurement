<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SupplierQuotation;
use App\Models\Canvass;
use App\Models\QuotationItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SupplierQuotationController extends Controller
{
    public function create(Canvass $canvass)
    {
        $user = Auth::user();
        
        // Canvassers can only add quotations to their own canvasses
        if ($user->hasRole('CANVASSER') && $canvass->canvasser_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        if (!Auth::user()->hasAnyRole(['CANVASSER', 'PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $canvass->load('rfq.purchaseRequest.prItems');
        return view('supplier-quotations.create', compact('canvass'));
    }

    public function store(Request $request, Canvass $canvass)
    {
        $user = Auth::user();
        
        if ($user->hasRole('CANVASSER') && $canvass->canvasser_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        if (!Auth::user()->hasAnyRole(['CANVASSER', 'PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_address' => 'nullable|string',
            'supplier_contact' => 'nullable|string|max:255',
            'supplier_email' => 'nullable|email|max:255',
            'submitted_date' => 'required|date',
            'delivery_days' => 'nullable|integer|min:0',
            'remarks' => 'nullable|string',
            'is_compliant' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.pr_item_id' => 'required|exists:pr_items,id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_of_measure' => 'required|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Calculate total quote price
            $totalPrice = 0;
            foreach ($validated['items'] as $item) {
                $totalPrice += $item['quantity'] * $item['unit_price'];
            }

            $quotation = SupplierQuotation::create([
                'canvass_id' => $canvass->id,
                'supplier_name' => $validated['supplier_name'],
                'supplier_address' => $validated['supplier_address'] ?? null,
                'supplier_contact' => $validated['supplier_contact'] ?? null,
                'supplier_email' => $validated['supplier_email'] ?? null,
                'submitted_date' => $validated['submitted_date'],
                'quote_price' => $totalPrice,
                'delivery_days' => $validated['delivery_days'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'is_compliant' => $request->has('is_compliant'),
                'is_selected' => false,
            ]);

            // Create quotation items
            foreach ($validated['items'] as $item) {
                QuotationItem::create([
                    'quotation_id' => $quotation->id,
                    'pr_item_id' => $item['pr_item_id'],
                    'item_name' => $item['item_name'],
                    'item_description' => $item['item_description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_of_measure' => $item['unit_of_measure'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            // Update canvass status to IN_PROGRESS if it's PENDING
            if ($canvass->status === 'PENDING') {
                $canvass->update(['status' => 'IN_PROGRESS']);
            }

            activity_log('CREATED', 'SUPPLIER_QUOTATION', $quotation->id, [
                'supplier_name' => $quotation->supplier_name,
                'quote_price' => $quotation->quote_price,
            ]);

            DB::commit();

            return redirect()->route('canvasses.show', $canvass)
                ->with('success', 'Supplier quotation added successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to add supplier quotation: ' . $e->getMessage()]);
        }
    }

    public function show(SupplierQuotation $supplierQuotation)
    {
        $user = Auth::user();
        $canvass = $supplierQuotation->canvass;
        
        if ($user->hasRole('CANVASSER') && $canvass->canvasser_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $supplierQuotation->load(['canvass.rfq.purchaseRequest', 'quotationItems.prItem']);
        return view('supplier-quotations.show', compact('supplierQuotation'));
    }

    public function edit(SupplierQuotation $supplierQuotation)
    {
        $user = Auth::user();
        $canvass = $supplierQuotation->canvass;
        
        if ($user->hasRole('CANVASSER') && $canvass->canvasser_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        if (!Auth::user()->hasAnyRole(['CANVASSER', 'PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $supplierQuotation->load(['canvass.rfq.purchaseRequest.prItems', 'quotationItems']);
        return view('supplier-quotations.edit', compact('supplierQuotation'));
    }

    public function update(Request $request, SupplierQuotation $supplierQuotation)
    {
        $user = Auth::user();
        $canvass = $supplierQuotation->canvass;
        
        if ($user->hasRole('CANVASSER') && $canvass->canvasser_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        if (!Auth::user()->hasAnyRole(['CANVASSER', 'PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'supplier_name' => 'required|string|max:255',
            'supplier_address' => 'nullable|string',
            'supplier_contact' => 'nullable|string|max:255',
            'supplier_email' => 'nullable|email|max:255',
            'submitted_date' => 'required|date',
            'delivery_days' => 'nullable|integer|min:0',
            'remarks' => 'nullable|string',
            'is_compliant' => 'boolean',
            'items' => 'required|array|min:1',
            'items.*.pr_item_id' => 'required|exists:pr_items,id',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_of_measure' => 'required|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Calculate total quote price
            $totalPrice = 0;
            foreach ($validated['items'] as $item) {
                $totalPrice += $item['quantity'] * $item['unit_price'];
            }

            $supplierQuotation->update([
                'supplier_name' => $validated['supplier_name'],
                'supplier_address' => $validated['supplier_address'] ?? null,
                'supplier_contact' => $validated['supplier_contact'] ?? null,
                'supplier_email' => $validated['supplier_email'] ?? null,
                'submitted_date' => $validated['submitted_date'],
                'quote_price' => $totalPrice,
                'delivery_days' => $validated['delivery_days'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
                'is_compliant' => $request->has('is_compliant'),
            ]);

            // Delete existing items and create new ones
            $supplierQuotation->quotationItems()->delete();
            foreach ($validated['items'] as $item) {
                QuotationItem::create([
                    'quotation_id' => $supplierQuotation->id,
                    'pr_item_id' => $item['pr_item_id'],
                    'item_name' => $item['item_name'],
                    'item_description' => $item['item_description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_of_measure' => $item['unit_of_measure'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            activity_log('UPDATED', 'SUPPLIER_QUOTATION', $supplierQuotation->id, [
                'supplier_name' => $supplierQuotation->supplier_name,
            ]);

            DB::commit();

            return redirect()->route('canvasses.show', $canvass)
                ->with('success', 'Supplier quotation updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update supplier quotation: ' . $e->getMessage()]);
        }
    }

    public function select(Request $request, SupplierQuotation $supplierQuotation)
    {
        if (!Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        // Unselect all other quotations for this canvass
        $supplierQuotation->canvass->supplierQuotations()
            ->where('id', '!=', $supplierQuotation->id)
            ->update(['is_selected' => false]);

        // Select this quotation
        $supplierQuotation->update(['is_selected' => true]);

        activity_log('SELECTED', 'SUPPLIER_QUOTATION', $supplierQuotation->id, [
            'supplier_name' => $supplierQuotation->supplier_name,
        ]);

        return back()->with('success', 'Supplier quotation selected successfully.');
    }

    public function destroy(SupplierQuotation $supplierQuotation)
    {
        $user = Auth::user();
        $canvass = $supplierQuotation->canvass;
        
        if ($user->hasRole('CANVASSER') && $canvass->canvasser_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }
        
        if (!Auth::user()->hasAnyRole(['CANVASSER', 'PROCUREMENT_OFFICER', 'ADMIN'])) {
            abort(403, 'Unauthorized action.');
        }

        activity_log('DELETED', 'SUPPLIER_QUOTATION', $supplierQuotation->id, [
            'supplier_name' => $supplierQuotation->supplier_name,
        ]);

        $supplierQuotation->delete();

        return redirect()->route('canvasses.show', $canvass)
            ->with('success', 'Supplier quotation deleted successfully.');
    }
}

