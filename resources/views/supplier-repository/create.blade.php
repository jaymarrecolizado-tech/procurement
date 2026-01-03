<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Add Quotation to Repository') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4">
                        <a href="{{ route('supplier-repository.index') }}" class="text-indigo-600 hover:text-indigo-900">
                            ← Back to Repository
                        </a>
                    </div>

                    <form method="POST" action="{{ route('supplier-repository.store') }}" enctype="multipart/form-data" id="quotationForm">
                        @csrf

                        <!-- Supplier Section -->
                        <div class="mb-6 border-b pb-6">
                            <h3 class="text-lg font-medium mb-4">Supplier Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Existing Supplier</label>
                                    <select name="supplier_id" id="supplier_select" class="w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="">-- Select Existing Supplier --</option>
                                        @foreach($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}">{{ $supplier->supplier_name_original }}</option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-gray-500">OR create new supplier below</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">New Supplier Name *</label>
                                    <input type="text" name="supplier_name_new" id="supplier_name_new" 
                                           class="w-full rounded-md border-gray-300 shadow-sm" 
                                           placeholder="Enter supplier name if creating new">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier Address</label>
                                    <input type="text" name="supplier_address" class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier Contact</label>
                                    <input type="text" name="supplier_contact" class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier Email</label>
                                    <input type="email" name="supplier_email" class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Supplier Category</label>
                                    <input type="text" name="supplier_category" class="w-full rounded-md border-gray-300 shadow-sm" 
                                           placeholder="e.g., IT Equipment, Office Supplies">
                                </div>
                            </div>
                        </div>

                        <!-- Quotation Details -->
                        <div class="mb-6 border-b pb-6">
                            <h3 class="text-lg font-medium mb-4">Quotation Details</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Link to RFQ (Optional)</label>
                                    <select name="rfq_id" class="w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="">-- None --</option>
                                        @foreach($rfqs as $rfq)
                                            <option value="{{ $rfq->id }}" {{ isset($prefillData) && $prefillData['type'] == 'rfq' && $prefillData['rfq_id'] == $rfq->id ? 'selected' : '' }}>
                                                {{ $rfq->rfq_number }} - {{ $rfq->purchaseRequest->project_title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Link to Canvass (Optional)</label>
                                    <select name="canvass_id" class="w-full rounded-md border-gray-300 shadow-sm">
                                        <option value="">-- None --</option>
                                        @foreach($canvasses as $canvass)
                                            <option value="{{ $canvass->id }}" {{ isset($prefillData) && $prefillData['type'] == 'canvass' && $prefillData['canvass_id'] == $canvass->id ? 'selected' : '' }}>
                                                {{ $canvass->rfq->rfq_number }} - {{ $canvass->task_description }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">RFQ Number (Reference)</label>
                                    <input type="text" name="rfq_number" value="{{ isset($prefillData) ? $prefillData['rfq_number'] : '' }}" 
                                           class="w-full rounded-md border-gray-300 shadow-sm" placeholder="RFQ-2025-0001">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quotation Date *</label>
                                    <input type="date" name="quotation_date" value="{{ date('Y-m-d') }}" required 
                                           class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Validity Period</label>
                                    <input type="date" name="validity_period" class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Days</label>
                                    <input type="number" name="delivery_days" min="0" class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Terms</label>
                                    <textarea name="payment_terms" rows="2" class="w-full rounded-md border-gray-300 shadow-sm"></textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
                                    <textarea name="remarks" rows="2" class="w-full rounded-md border-gray-300 shadow-sm"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Items Section (Bulk Entry) -->
                        <div class="mb-6 border-b pb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium">Quotation Items *</h3>
                                <button type="button" id="add-item-btn" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                                    + Add Item
                                </button>
                            </div>

                            <div id="items-container" class="space-y-4">
                                <!-- Items will be added here dynamically -->
                            </div>
                        </div>

                        <!-- Image Upload Section -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-4">Quotation Images</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Images (Multiple allowed)</label>
                                <input type="file" name="images[]" multiple accept="image/*,.pdf" 
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="mt-1 text-xs text-gray-500">Max size: 5MB per file. Allowed: JPG, PNG, PDF</p>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('supplier-repository.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Save Quotation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let itemCount = 0;

        function addItemField(itemData = null) {
            const container = document.getElementById('items-container');
            const itemDiv = document.createElement('div');
            itemDiv.className = 'border rounded-md p-4 bg-gray-50';
            itemDiv.id = `item-${itemCount}`;
            
            itemDiv.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Item Name *</label>
                        <input type="text" name="items[${itemCount}][item_name]" value="${itemData?.item_name || ''}" required 
                               class="w-full rounded-md border-gray-300 shadow-sm" placeholder="Enter item name">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Item Description</label>
                        <textarea name="items[${itemCount}][item_description]" rows="2" 
                                  class="w-full rounded-md border-gray-300 shadow-sm" placeholder="Item description">${itemData?.item_description || ''}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                        <input type="number" name="items[${itemCount}][quantity]" value="${itemData?.quantity || ''}" step="0.01" min="0.01" required 
                               class="w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unit of Measure *</label>
                        <input type="text" name="items[${itemCount}][unit_of_measure]" value="${itemData?.unit_of_measure || ''}" required 
                               class="w-full rounded-md border-gray-300 shadow-sm" placeholder="pcs, units, etc.">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price *</label>
                        <input type="number" name="items[${itemCount}][unit_price]" value="${itemData?.estimated_price || ''}" step="0.01" min="0" required 
                               class="w-full rounded-md border-gray-300 shadow-sm" placeholder="0.00">
                    </div>
                    <div class="flex items-end">
                        <button type="button" onclick="removeItem(${itemCount})" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm">
                            Remove Item
                        </button>
                    </div>
                </div>
            `;
            
            container.appendChild(itemDiv);
            itemCount++;
        }

        function removeItem(id) {
            const itemDiv = document.getElementById(`item-${id}`);
            if (itemDiv) {
                itemDiv.remove();
            }
        }

        document.getElementById('add-item-btn').addEventListener('click', () => addItemField());

        // Pre-fill items if coming from RFQ/Canvass
        @if(isset($prefillData) && isset($prefillData['items']))
            @foreach($prefillData['items'] as $item)
                addItemField({
                    item_name: '{{ $item->item_name }}',
                    item_description: '{{ $item->item_description ?? '' }}',
                    quantity: '{{ $item->quantity }}',
                    unit_of_measure: '{{ $item->unit_of_measure }}',
                    estimated_price: '{{ $item->estimated_price }}'
                });
            @endforeach
        @else
            // Add one empty item field by default
            addItemField();
        @endif

        // Toggle supplier fields based on selection
        document.getElementById('supplier_select').addEventListener('change', function() {
            if (this.value) {
                document.getElementById('supplier_name_new').disabled = true;
            } else {
                document.getElementById('supplier_name_new').disabled = false;
            }
        });
    </script>
</x-app-layout>

