<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Supplier Quotation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Canvass Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">RFQ Number</p>
                            <p class="font-semibold">{{ $supplierQuotation->canvass->rfq->rfq_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">PR Number</p>
                            <p class="font-semibold">{{ $supplierQuotation->canvass->rfq->purchaseRequest->pr_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Project Title</p>
                            <p class="font-semibold">{{ $supplierQuotation->canvass->rfq->purchaseRequest->project_title }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('supplier-quotations.update', $supplierQuotation) }}" id="quotationForm">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <!-- Supplier Information -->
                            <div>
                                <h3 class="text-lg font-medium mb-4">Supplier Information</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="supplier_name" :value="__('Supplier Name')" />
                                        <x-text-input id="supplier_name" class="block mt-1 w-full" type="text" name="supplier_name" :value="old('supplier_name', $supplierQuotation->supplier_name)" required />
                                        <x-input-error :messages="$errors->get('supplier_name')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="supplier_contact" :value="__('Contact Number')" />
                                        <x-text-input id="supplier_contact" class="block mt-1 w-full" type="text" name="supplier_contact" :value="old('supplier_contact', $supplierQuotation->supplier_contact)" />
                                        <x-input-error :messages="$errors->get('supplier_contact')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="supplier_email" :value="__('Email')" />
                                        <x-text-input id="supplier_email" class="block mt-1 w-full" type="email" name="supplier_email" :value="old('supplier_email', $supplierQuotation->supplier_email)" />
                                        <x-input-error :messages="$errors->get('supplier_email')" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-input-label for="submitted_date" :value="__('Submitted Date')" />
                                        <x-text-input id="submitted_date" class="block mt-1 w-full" type="date" name="submitted_date" :value="old('submitted_date', $supplierQuotation->submitted_date->format('Y-m-d'))" required />
                                        <x-input-error :messages="$errors->get('submitted_date')" class="mt-2" />
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <x-input-label for="supplier_address" :value="__('Address')" />
                                    <textarea id="supplier_address" name="supplier_address" rows="2" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('supplier_address', $supplierQuotation->supplier_address) }}</textarea>
                                    <x-input-error :messages="$errors->get('supplier_address')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Quotation Details -->
                            <div>
                                <h3 class="text-lg font-medium mb-4">Quotation Details</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <x-input-label for="delivery_days" :value="__('Delivery Days')" />
                                        <x-text-input id="delivery_days" class="block mt-1 w-full" type="number" name="delivery_days" :value="old('delivery_days', $supplierQuotation->delivery_days)" min="0" />
                                        <x-input-error :messages="$errors->get('delivery_days')" class="mt-2" />
                                    </div>
                                    <div class="flex items-center mt-6">
                                        <input id="is_compliant" name="is_compliant" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" {{ old('is_compliant', $supplierQuotation->is_compliant) ? 'checked' : '' }}>
                                        <x-input-label for="is_compliant" :value="__('Is Compliant')" class="ml-2" />
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <x-input-label for="remarks" :value="__('Remarks')" />
                                    <textarea id="remarks" name="remarks" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('remarks', $supplierQuotation->remarks) }}</textarea>
                                    <x-input-error :messages="$errors->get('remarks')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Quotation Items -->
                            <div>
                                <h3 class="text-lg font-medium mb-4">Quotation Items</h3>
                                <div id="items-container">
                                    @foreach($supplierQuotation->canvass->rfq->purchaseRequest->prItems as $index => $prItem)
                                        @php
                                            $quotationItem = $supplierQuotation->quotationItems->firstWhere('pr_item_id', $prItem->id);
                                        @endphp
                                        <div class="border rounded-lg p-4 mb-4 item-row" data-item-index="{{ $index }}">
                                            <input type="hidden" name="items[{{ $index }}][pr_item_id]" value="{{ $prItem->id }}">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                                <div>
                                                    <x-input-label :value="__('Item Code')" />
                                                    <p class="mt-1 text-sm text-gray-600">{{ $prItem->item_code }}</p>
                                                </div>
                                                <div>
                                                    <x-input-label :value="__('Item Name')" />
                                                    <p class="mt-1 text-sm text-gray-600">{{ $prItem->item_name }}</p>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                                <div>
                                                    <x-input-label :for="'items_' . $index . '_quantity'" :value="__('Quantity')" />
                                                    <x-text-input :id="'items_' . $index . '_quantity'" class="block mt-1 w-full" type="number" step="0.01" :name="'items[' . $index . '][quantity]'" :value="old('items.' . $index . '.quantity', $quotationItem ? $quotationItem->quantity : $prItem->quantity)" required />
                                                    <x-input-error :messages="$errors->get('items.' . $index . '.quantity')" class="mt-2" />
                                                </div>
                                                <div>
                                                    <x-input-label :for="'items_' . $index . '_unit_of_measure'" :value="__('Unit')" />
                                                    <x-text-input :id="'items_' . $index . '_unit_of_measure'" class="block mt-1 w-full" type="text" :name="'items[' . $index . '][unit_of_measure]'" :value="old('items.' . $index . '.unit_of_measure', $quotationItem ? $quotationItem->unit_of_measure : $prItem->unit_of_measure)" required />
                                                    <x-input-error :messages="$errors->get('items.' . $index . '.unit_of_measure')" class="mt-2" />
                                                </div>
                                                <div>
                                                    <x-input-label :for="'items_' . $index . '_unit_price'" :value="__('Unit Price')" />
                                                    <x-text-input :id="'items_' . $index . '_unit_price'" class="block mt-1 w-full item-unit-price" type="number" step="0.01" :name="'items[' . $index . '][unit_price]'" :value="old('items.' . $index . '.unit_price', $quotationItem ? $quotationItem->unit_price : '')" required />
                                                    <x-input-error :messages="$errors->get('items.' . $index . '.unit_price')" class="mt-2" />
                                                </div>
                                                <div>
                                                    <x-input-label :value="__('Total')" />
                                                    <p class="mt-1 text-sm font-semibold item-total">₱0.00</p>
                                                </div>
                                            </div>
                                            <div class="mt-4">
                                                <x-input-label :for="'items_' . $index . '_item_name'" :value="__('Item Name (Override)')" />
                                                <x-text-input :id="'items_' . $index . '_item_name'" class="block mt-1 w-full" type="text" :name="'items[' . $index . '][item_name]'" :value="old('items.' . $index . '.item_name', $quotationItem ? $quotationItem->item_name : $prItem->item_name)" required />
                                                <x-input-error :messages="$errors->get('items.' . $index . '.item_name')" class="mt-2" />
                                            </div>
                                            <div class="mt-4">
                                                <x-input-label :for="'items_' . $index . '_item_description'" :value="__('Item Description')" />
                                                <textarea :id="'items_' . $index . '_item_description'" :name="'items[' . $index . '][item_description]'" rows="2" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('items.' . $index . '.item_description', $quotationItem ? $quotationItem->item_description : $prItem->item_description) }}</textarea>
                                                <x-input-error :messages="$errors->get('items.' . $index . '.item_description')" class="mt-2" />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-4 p-4 bg-gray-50 rounded-lg">
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-semibold">Total Quote Price:</span>
                                        <span class="text-xl font-bold text-indigo-600" id="total-quote-price">₱0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 gap-4">
                            <a href="{{ route('canvasses.show', $supplierQuotation->canvass) }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Update Quotation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function calculateTotals() {
                let grandTotal = 0;
                document.querySelectorAll('.item-row').forEach(function(row) {
                    const quantity = parseFloat(row.querySelector('.item-unit-price').previousElementSibling.previousElementSibling.querySelector('input').value) || 0;
                    const unitPrice = parseFloat(row.querySelector('.item-unit-price').value) || 0;
                    const total = quantity * unitPrice;
                    row.querySelector('.item-total').textContent = '₱' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    grandTotal += total;
                });
                document.getElementById('total-quote-price').textContent = '₱' + grandTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }

            document.querySelectorAll('.item-unit-price, input[name*="[quantity]"]').forEach(function(input) {
                input.addEventListener('input', calculateTotals);
            });

            calculateTotals();
        });
    </script>
</x-app-layout>

