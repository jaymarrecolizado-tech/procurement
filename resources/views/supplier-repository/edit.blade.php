<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Quotation History') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4">
                        <a href="{{ route('supplier-repository.show', $quotationHistory) }}" class="text-indigo-600 hover:text-indigo-900">
                            ← Back to Quotation
                        </a>
                    </div>

                    <form method="POST" action="{{ route('supplier-repository.update', $quotationHistory) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Supplier Section -->
                        <div class="mb-6 border-b pb-6">
                            <h3 class="text-lg font-medium mb-4">Supplier</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Supplier *</label>
                                <select name="supplier_id" required class="w-full rounded-md border-gray-300 shadow-sm">
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ $quotationHistory->supplier_id == $supplier->id ? 'selected' : '' }}>
                                            {{ $supplier->supplier_name_original }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Item Information -->
                        <div class="mb-6 border-b pb-6">
                            <h3 class="text-lg font-medium mb-4">Item Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Item Name *</label>
                                    <input type="text" name="item_name" value="{{ old('item_name', $quotationHistory->item_name) }}" required 
                                           class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Item Description</label>
                                    <textarea name="item_description" rows="3" class="w-full rounded-md border-gray-300 shadow-sm">{{ old('item_description', $quotationHistory->item_description) }}</textarea>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                                    <input type="number" name="quantity" value="{{ old('quantity', $quotationHistory->quantity) }}" step="0.01" min="0.01" required 
                                           class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit of Measure *</label>
                                    <input type="text" name="unit_of_measure" value="{{ old('unit_of_measure', $quotationHistory->unit_of_measure) }}" required 
                                           class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Unit Price *</label>
                                    <input type="number" name="unit_price" value="{{ old('unit_price', $quotationHistory->unit_price) }}" step="0.01" min="0" required 
                                           class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Price</label>
                                    <input type="text" value="₱{{ number_format($quotationHistory->quantity * $quotationHistory->unit_price, 2) }}" 
                                           class="w-full rounded-md border-gray-300 shadow-sm bg-gray-50" readonly>
                                </div>
                            </div>
                        </div>

                        <!-- Quotation Details -->
                        <div class="mb-6 border-b pb-6">
                            <h3 class="text-lg font-medium mb-4">Quotation Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Quotation Date *</label>
                                    <input type="date" name="quotation_date" value="{{ old('quotation_date', $quotationHistory->quotation_date->format('Y-m-d')) }}" required 
                                           class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">RFQ Number</label>
                                    <input type="text" name="rfq_number" value="{{ old('rfq_number', $quotationHistory->rfq_number) }}" 
                                           class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Validity Period</label>
                                    <input type="date" name="validity_period" value="{{ old('validity_period', $quotationHistory->validity_period ? $quotationHistory->validity_period->format('Y-m-d') : '') }}" 
                                           class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Days</label>
                                    <input type="number" name="delivery_days" value="{{ old('delivery_days', $quotationHistory->delivery_days) }}" min="0" 
                                           class="w-full rounded-md border-gray-300 shadow-sm">
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Terms</label>
                                    <textarea name="payment_terms" rows="2" class="w-full rounded-md border-gray-300 shadow-sm">{{ old('payment_terms', $quotationHistory->payment_terms) }}</textarea>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Remarks</label>
                                    <textarea name="remarks" rows="2" class="w-full rounded-md border-gray-300 shadow-sm">{{ old('remarks', $quotationHistory->remarks) }}</textarea>
                                </div>
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="is_selected" value="1" {{ old('is_selected', $quotationHistory->is_selected) ? 'checked' : '' }} 
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-gray-700">Was this quotation selected?</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Existing Images -->
                        @if($quotationHistory->images->count() > 0)
                            <div class="mb-6 border-b pb-6">
                                <h3 class="text-lg font-medium mb-4">Existing Images</h3>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                    @foreach($quotationHistory->images as $image)
                                        <div class="border rounded-md p-2">
                                            <img src="{{ Storage::url($image->image_path) }}" alt="Quotation Image" class="w-full h-32 object-cover rounded">
                                            <label class="flex items-center mt-2">
                                                <input type="checkbox" name="delete_images[]" value="{{ $image->id }}" 
                                                       class="rounded border-gray-300 text-red-600">
                                                <span class="ml-2 text-xs text-red-600">Delete</span>
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- New Images -->
                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-4">Add New Images</h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Images (Multiple allowed)</label>
                                <input type="file" name="new_images[]" multiple accept="image/*,.pdf" 
                                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                <p class="mt-1 text-xs text-gray-500">Max size: 5MB per file. Allowed: JPG, PNG, PDF</p>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex justify-end space-x-4">
                            <a href="{{ route('supplier-repository.show', $quotationHistory) }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Update Quotation
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

