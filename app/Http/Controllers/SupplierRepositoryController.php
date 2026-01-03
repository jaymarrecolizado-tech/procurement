<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use App\Models\SupplierQuotationHistory;
use App\Models\QuotationImage;
use App\Models\SupplierHistoryActivityLog;
use App\Models\RFQ;
use App\Models\Canvass;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SupplierRepositoryController extends Controller
{
    /**
     * Display supplier quotation history with search
     */
    public function index(Request $request)
    {
        $query = SupplierQuotationHistory::with(['supplier', 'enteredBy', 'images']);

        // Wildcard search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('item_name', 'LIKE', "%{$search}%")
                  ->orWhere('item_code', 'LIKE', "%{$search}%")
                  ->orWhere('item_description', 'LIKE', "%{$search}%")
                  ->orWhere('rfq_number', 'LIKE', "%{$search}%")
                  ->orWhereHas('supplier', function($sq) use ($search) {
                      $sq->where('supplier_name', 'LIKE', "%{$search}%")
                         ->orWhere('supplier_name_original', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Filter by supplier
        if ($request->has('supplier_id') && $request->supplier_id) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->start_date) {
            $query->where('quotation_date', '>=', $request->start_date);
        }
        if ($request->has('end_date') && $request->end_date) {
            $query->where('quotation_date', '<=', $request->end_date);
        }

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->whereHas('supplier', function($q) use ($request) {
                $q->where('supplier_category', $request->category);
            });
        }

        $quotations = $query->latest('quotation_date')->paginate(20);
        $suppliers = Supplier::where('status', 'ACTIVE')->orderBy('supplier_name')->get();
        $categories = Supplier::distinct()->whereNotNull('supplier_category')->pluck('supplier_category');

        return view('supplier-repository.index', compact('quotations', 'suppliers', 'categories'));
    }

    /**
     * Show form to create new quotation history entry
     */
    public function create(Request $request)
    {
        $suppliers = Supplier::where('status', 'ACTIVE')->orderBy('supplier_name')->get();
        $rfqs = RFQ::with('purchaseRequest')->latest()->limit(50)->get();
        $canvasses = Canvass::with('rfq.purchaseRequest')->latest()->limit(50)->get();

        // Pre-fill from RFQ or Canvass if provided
        $prefillData = null;
        if ($request->has('rfq_id')) {
            $rfq = RFQ::with('purchaseRequest.prItems')->find($request->rfq_id);
            if ($rfq) {
                $prefillData = [
                    'type' => 'rfq',
                    'rfq_id' => $rfq->id,
                    'rfq_number' => $rfq->rfq_number,
                    'items' => $rfq->purchaseRequest->prItems,
                ];
            }
        } elseif ($request->has('canvass_id')) {
            $canvass = Canvass::with('rfq.purchaseRequest.prItems')->find($request->canvass_id);
            if ($canvass) {
                $prefillData = [
                    'type' => 'canvass',
                    'canvass_id' => $canvass->id,
                    'rfq_number' => $canvass->rfq->rfq_number,
                    'items' => $canvass->rfq->purchaseRequest->prItems,
                ];
            }
        }

        return view('supplier-repository.create', compact('suppliers', 'rfqs', 'canvasses', 'prefillData'));
    }

    /**
     * Store new quotation history entry (with bulk items)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'nullable|exists:suppliers,id',
            'supplier_name_new' => 'required_without:supplier_id|string|max:255',
            'supplier_address' => 'nullable|string',
            'supplier_contact' => 'nullable|string|max:255',
            'supplier_email' => 'nullable|email|max:255',
            'supplier_category' => 'nullable|string|max:255',
            'rfq_id' => 'nullable|exists:rfqs,id',
            'canvass_id' => 'nullable|exists:canvasses,id',
            'quotation_id' => 'nullable|exists:supplier_quotations,id',
            'rfq_number' => 'nullable|string|max:255',
            'quotation_date' => 'required|date',
            'validity_period' => 'nullable|date',
            'delivery_days' => 'nullable|integer|min:0',
            'payment_terms' => 'nullable|string',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:255',
            'items.*.item_description' => 'nullable|string',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_of_measure' => 'required|string|max:50',
            'items.*.unit_price' => 'required|numeric|min:0',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpeg,jpg,png,pdf|max:5120', // 5MB max
        ]);

        DB::beginTransaction();
        try {
            // Create or get supplier
            if ($request->supplier_id) {
                $supplier = Supplier::find($request->supplier_id);
            } else {
                $normalizedName = Supplier::normalizeName($validated['supplier_name_new']);
                
                // Check for existing supplier with similar name
                $existingSupplier = Supplier::where('supplier_name', $normalizedName)->first();
                
                if ($existingSupplier) {
                    $supplier = $existingSupplier;
                } else {
                    $supplier = Supplier::create([
                        'supplier_name' => $normalizedName,
                        'supplier_name_original' => $validated['supplier_name_new'],
                        'supplier_address' => $validated['supplier_address'] ?? null,
                        'supplier_contact' => $validated['supplier_contact'] ?? null,
                        'supplier_email' => $validated['supplier_email'] ?? null,
                        'supplier_category' => $validated['supplier_category'] ?? null,
                        'status' => 'ACTIVE',
                        'created_by' => Auth::id(),
                    ]);
                }
            }

            // Create quotation history entries for each item
            $createdQuotations = [];
            foreach ($validated['items'] as $item) {
                $itemCode = SupplierQuotationHistory::generateItemCode($item['item_name']);
                $totalPrice = $item['quantity'] * $item['unit_price'];

                $quotation = SupplierQuotationHistory::create([
                    'supplier_id' => $supplier->id,
                    'rfq_id' => $validated['rfq_id'] ?? null,
                    'canvass_id' => $validated['canvass_id'] ?? null,
                    'quotation_id' => $validated['quotation_id'] ?? null,
                    'item_name' => $item['item_name'],
                    'item_code' => $itemCode,
                    'item_description' => $item['item_description'] ?? null,
                    'quantity' => $item['quantity'],
                    'unit_of_measure' => $item['unit_of_measure'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $totalPrice,
                    'quotation_date' => $validated['quotation_date'],
                    'rfq_number' => $validated['rfq_number'] ?? null,
                    'delivery_days' => $validated['delivery_days'] ?? null,
                    'payment_terms' => $validated['payment_terms'] ?? null,
                    'validity_period' => $validated['validity_period'] ?? null,
                    'remarks' => $validated['remarks'] ?? null,
                    'is_selected' => false,
                    'entered_by' => Auth::id(),
                ]);

                // Log creation
                SupplierHistoryActivityLog::create([
                    'quotation_history_id' => $quotation->id,
                    'action' => 'CREATED',
                    'field_name' => null,
                    'old_value' => null,
                    'new_value' => 'Quotation created',
                    'changed_by' => Auth::id(),
                ]);

                $createdQuotations[] = $quotation;
            }

            // Handle image uploads (attach to first quotation if multiple items)
            if ($request->hasFile('images')) {
                $targetQuotation = $createdQuotations[0]; // Attach all images to first quotation
                
                foreach ($request->file('images') as $image) {
                    $fileName = time() . '_' . $image->getClientOriginalName();
                    $filePath = $image->storeAs('supplier-quotations/' . $targetQuotation->id, $fileName, 'public');

                    QuotationImage::create([
                        'quotation_history_id' => $targetQuotation->id,
                        'image_path' => $filePath,
                        'original_filename' => $image->getClientOriginalName(),
                        'file_size' => $image->getSize(),
                        'mime_type' => $image->getMimeType(),
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }

            activity_log('CREATED', 'SUPPLIER_QUOTATION_HISTORY', $createdQuotations[0]->id, [
                'supplier' => $supplier->supplier_name,
                'items_count' => count($createdQuotations),
            ]);

            DB::commit();

            return redirect()->route('supplier-repository.index')
                ->with('success', 'Quotation history entry created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create quotation history: ' . $e->getMessage()]);
        }
    }

    /**
     * Show quotation history details
     */
    public function show(SupplierQuotationHistory $quotationHistory)
    {
        $quotationHistory->load(['supplier', 'rfq.purchaseRequest', 'canvass.rfq.purchaseRequest', 'enteredBy', 'images.uploader', 'activityLogs.changedBy']);
        
        return view('supplier-repository.show', compact('quotationHistory'));
    }

    /**
     * Show form to edit quotation history
     */
    public function edit(SupplierQuotationHistory $quotationHistory)
    {
        $quotationHistory->load(['supplier', 'images']);
        $suppliers = Supplier::where('status', 'ACTIVE')->orderBy('supplier_name')->get();
        $categories = Supplier::distinct()->whereNotNull('supplier_category')->pluck('supplier_category');

        return view('supplier-repository.edit', compact('quotationHistory', 'suppliers', 'categories'));
    }

    /**
     * Update quotation history with activity logging
     */
    public function update(Request $request, SupplierQuotationHistory $quotationHistory)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'item_name' => 'required|string|max:255',
            'item_description' => 'nullable|string',
            'quantity' => 'required|numeric|min:0.01',
            'unit_of_measure' => 'required|string|max:50',
            'unit_price' => 'required|numeric|min:0',
            'quotation_date' => 'required|date',
            'rfq_number' => 'nullable|string|max:255',
            'delivery_days' => 'nullable|integer|min:0',
            'payment_terms' => 'nullable|string',
            'validity_period' => 'nullable|date',
            'remarks' => 'nullable|string',
            'is_selected' => 'boolean',
            'new_images' => 'nullable|array',
            'new_images.*' => 'image|mimes:jpeg,jpg,png,pdf|max:5120',
            'delete_images' => 'nullable|array',
        ]);

        DB::beginTransaction();
        try {
            $oldData = $quotationHistory->toArray();
            
            // Update item code if item name changed
            if ($quotationHistory->item_name !== $validated['item_name']) {
                $validated['item_code'] = SupplierQuotationHistory::generateItemCode($validated['item_name']);
            }

            // Calculate total price
            $validated['total_price'] = $validated['quantity'] * $validated['unit_price'];

            // Log changes for each field
            $fieldsToLog = [
                'supplier_id', 'item_name', 'item_code', 'item_description', 'quantity',
                'unit_of_measure', 'unit_price', 'total_price', 'quotation_date',
                'rfq_number', 'delivery_days', 'payment_terms', 'validity_period',
                'remarks', 'is_selected'
            ];

            foreach ($fieldsToLog as $field) {
                if (isset($validated[$field]) && $oldData[$field] != $validated[$field]) {
                    SupplierHistoryActivityLog::create([
                        'quotation_history_id' => $quotationHistory->id,
                        'action' => 'UPDATED',
                        'field_name' => $field,
                        'old_value' => $oldData[$field] ?? null,
                        'new_value' => $validated[$field],
                        'changed_by' => Auth::id(),
                    ]);
                }
            }

            // Update quotation
            $quotationHistory->update($validated);

            // Handle image deletions
            if ($request->has('delete_images')) {
                foreach ($request->delete_images as $imageId) {
                    $image = QuotationImage::find($imageId);
                    if ($image && $image->quotation_history_id === $quotationHistory->id) {
                        if (Storage::disk('public')->exists($image->image_path)) {
                            Storage::disk('public')->delete($image->image_path);
                        }
                        $image->delete();
                    }
                }
            }

            // Handle new image uploads
            if ($request->hasFile('new_images')) {
                foreach ($request->file('new_images') as $image) {
                    $fileName = time() . '_' . $image->getClientOriginalName();
                    $filePath = $image->storeAs('supplier-quotations/' . $quotationHistory->id, $fileName, 'public');

                    QuotationImage::create([
                        'quotation_history_id' => $quotationHistory->id,
                        'image_path' => $filePath,
                        'original_filename' => $image->getClientOriginalName(),
                        'file_size' => $image->getSize(),
                        'mime_type' => $image->getMimeType(),
                        'uploaded_by' => Auth::id(),
                    ]);
                }
            }

            activity_log('UPDATED', 'SUPPLIER_QUOTATION_HISTORY', $quotationHistory->id, [
                'item_name' => $quotationHistory->item_name,
            ]);

            DB::commit();

            return redirect()->route('supplier-repository.show', $quotationHistory)
                ->with('success', 'Quotation history updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update quotation history: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete quotation history
     */
    public function destroy(SupplierQuotationHistory $quotationHistory)
    {
        DB::beginTransaction();
        try {
            // Log deletion
            SupplierHistoryActivityLog::create([
                'quotation_history_id' => $quotationHistory->id,
                'action' => 'DELETED',
                'field_name' => null,
                'old_value' => 'Quotation record',
                'new_value' => null,
                'changed_by' => Auth::id(),
            ]);

            // Delete images
            foreach ($quotationHistory->images as $image) {
                if (Storage::disk('public')->exists($image->image_path)) {
                    Storage::disk('public')->delete($image->image_path);
                }
                $image->delete();
            }

            activity_log('DELETED', 'SUPPLIER_QUOTATION_HISTORY', $quotationHistory->id, [
                'item_name' => $quotationHistory->item_name,
            ]);

            $quotationHistory->delete();

            DB::commit();

            return redirect()->route('supplier-repository.index')
                ->with('success', 'Quotation history deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete quotation history: ' . $e->getMessage()]);
        }
    }

    /**
     * Get suppliers for autocomplete
     */
    public function getSuppliers(Request $request)
    {
        $search = $request->get('search', '');
        
        $suppliers = Supplier::where('status', 'ACTIVE')
            ->where(function($q) use ($search) {
                $q->where('supplier_name', 'LIKE', "%{$search}%")
                  ->orWhere('supplier_name_original', 'LIKE', "%{$search}%");
            })
            ->limit(20)
            ->get(['id', 'supplier_name_original as name']);

        return response()->json($suppliers);
    }
}
