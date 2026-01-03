<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create Purchase Order') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4">
                        <a href="{{ route('purchase-requests.show', $purchaseRequest) }}" class="text-indigo-600 hover:text-indigo-900">
                            ← Back to Purchase Request
                        </a>
                    </div>

                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Purchase Request Information</h3>
                        <p class="text-sm text-gray-600">PR Number: <strong>{{ $purchaseRequest->pr_number }}</strong></p>
                        <p class="text-sm text-gray-600">Project Title: <strong>{{ $purchaseRequest->project_title }}</strong></p>
                    </div>

                    <div class="mb-6 bg-green-50 border border-green-200 rounded-md p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Selected Supplier</h3>
                        <p class="text-sm text-gray-600">Supplier: <strong>{{ $selectedQuotation->supplier_name }}</strong></p>
                        <p class="text-sm text-gray-600">Address: <strong>{{ $selectedQuotation->supplier_address ?? 'N/A' }}</strong></p>
                        <p class="text-sm text-gray-600">Contact: <strong>{{ $selectedQuotation->supplier_contact ?? 'N/A' }}</strong></p>
                        <p class="text-sm text-gray-600">Contract Amount: <strong>₱{{ number_format($selectedQuotation->quote_price, 2) }}</strong></p>
                        <p class="text-sm text-gray-600">Delivery Days: <strong>{{ $selectedQuotation->delivery_days ?? 'N/A' }} days</strong></p>
                    </div>

                    <form method="POST" action="{{ route('purchase-orders.store', $purchaseRequest) }}">
                        @csrf

                        <div class="space-y-6">
                            <div>
                                <label for="delivery_instructions" class="block text-sm font-medium text-gray-700">Delivery Instructions</label>
                                <textarea name="delivery_instructions" id="delivery_instructions" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('delivery_instructions', $purchaseRequest->rfq->delivery_schedule ?? '') }}</textarea>
                                @error('delivery_instructions')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="payment_terms" class="block text-sm font-medium text-gray-700">Payment Terms</label>
                                <textarea name="payment_terms" id="payment_terms" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('payment_terms', $purchaseRequest->rfq->payment_terms ?? '') }}</textarea>
                                @error('payment_terms')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="delivery_deadline" class="block text-sm font-medium text-gray-700">Delivery Deadline *</label>
                                <input type="date" name="delivery_deadline" id="delivery_deadline" value="{{ old('delivery_deadline', $selectedQuotation->delivery_days ? now()->addDays($selectedQuotation->delivery_days)->format('Y-m-d') : now()->addDays(30)->format('Y-m-d')) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @error('delivery_deadline')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end space-x-4">
                            <a href="{{ route('purchase-requests.show', $purchaseRequest) }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Create Purchase Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

