<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Purchase Order: ') . $purchaseOrder->po_number }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4">
                        <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="text-indigo-600 hover:text-indigo-900">
                            ← Back to Purchase Order
                        </a>
                    </div>

                    <form method="POST" action="{{ route('purchase-orders.update', $purchaseOrder) }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="supplier_name" class="block text-sm font-medium text-gray-700">Supplier Name *</label>
                                    <input type="text" name="supplier_name" id="supplier_name" value="{{ old('supplier_name', $purchaseOrder->supplier_name) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('supplier_name')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="supplier_contact" class="block text-sm font-medium text-gray-700">Supplier Contact</label>
                                    <input type="text" name="supplier_contact" id="supplier_contact" value="{{ old('supplier_contact', $purchaseOrder->supplier_contact) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('supplier_contact')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="supplier_address" class="block text-sm font-medium text-gray-700">Supplier Address</label>
                                    <textarea name="supplier_address" id="supplier_address" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('supplier_address', $purchaseOrder->supplier_address) }}</textarea>
                                    @error('supplier_address')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="contract_amount" class="block text-sm font-medium text-gray-700">Contract Amount *</label>
                                    <div class="mt-1 relative rounded-md shadow-sm">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">₱</span>
                                        </div>
                                        <input type="number" step="0.01" name="contract_amount" id="contract_amount" value="{{ old('contract_amount', $purchaseOrder->contract_amount) }}" required class="block w-full pl-7 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    @error('contract_amount')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="delivery_deadline" class="block text-sm font-medium text-gray-700">Delivery Deadline *</label>
                                    <input type="date" name="delivery_deadline" id="delivery_deadline" value="{{ old('delivery_deadline', $purchaseOrder->delivery_deadline->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    @error('delivery_deadline')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="delivery_instructions" class="block text-sm font-medium text-gray-700">Delivery Instructions</label>
                                    <textarea name="delivery_instructions" id="delivery_instructions" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('delivery_instructions', $purchaseOrder->delivery_instructions) }}</textarea>
                                    @error('delivery_instructions')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="payment_terms" class="block text-sm font-medium text-gray-700">Payment Terms</label>
                                    <textarea name="payment_terms" id="payment_terms" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('payment_terms', $purchaseOrder->payment_terms) }}</textarea>
                                    @error('payment_terms')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2">
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                    <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                                    @error('notes')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end space-x-4">
                            <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Update Purchase Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

