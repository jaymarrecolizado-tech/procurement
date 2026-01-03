<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create BAC Document') }}
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

                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900">Purchase Request Information</h3>
                        <p class="text-sm text-gray-600">PR Number: <strong>{{ $purchaseRequest->pr_number }}</strong></p>
                        <p class="text-sm text-gray-600">Project Title: <strong>{{ $purchaseRequest->project_title }}</strong></p>
                    </div>

                    @if($supplierQuotations->count() === 0)
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                            <p class="text-yellow-800">No supplier quotations found. Please complete canvassing first.</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('bac-documents.store', $purchaseRequest) }}">
                        @csrf

                        <div class="space-y-6">
                            <div>
                                <label for="document_type" class="block text-sm font-medium text-gray-700">Document Type *</label>
                                <select name="document_type" id="document_type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Document Type</option>
                                    @if(!in_array('ABSTRACT_OF_QUOTATIONS', $existingDocuments))
                                        <option value="ABSTRACT_OF_QUOTATIONS">Abstract of Quotations</option>
                                    @endif
                                    @if(!in_array('PRICE_MATRIX', $existingDocuments))
                                        <option value="PRICE_MATRIX">Price Matrix</option>
                                    @endif
                                    @if(!in_array('TWG_CERT', $existingDocuments))
                                        <option value="TWG_CERT">TWG Certificate</option>
                                    @endif
                                    @if(!in_array('RECOMMENDATION', $existingDocuments))
                                        <option value="RECOMMENDATION">Recommendation</option>
                                    @endif
                                    @if(!in_array('RESOLUTION', $existingDocuments))
                                        <option value="RESOLUTION">Resolution</option>
                                    @endif
                                </select>
                                @error('document_type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @if(count($existingDocuments) > 0)
                                    <p class="mt-1 text-sm text-gray-500">Existing documents: {{ implode(', ', array_map(function($type) {
                                        return match($type) {
                                            'ABSTRACT_OF_QUOTATIONS' => 'Abstract of Quotations',
                                            'PRICE_MATRIX' => 'Price Matrix',
                                            'TWG_CERT' => 'TWG Certificate',
                                            'RECOMMENDATION' => 'Recommendation',
                                            'RESOLUTION' => 'Resolution',
                                            default => $type
                                        };
                                    }, $existingDocuments)) }}</p>
                                @endif
                            </div>

                            <div>
                                <label for="procurement_mode" class="block text-sm font-medium text-gray-700">Procurement Mode</label>
                                <select name="procurement_mode" id="procurement_mode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Procurement Mode</option>
                                    <option value="SHOPPING">Shopping</option>
                                    <option value="SVP">Small Value Procurement (SVP)</option>
                                    <option value="PUBLIC_BIDDING">Public Bidding</option>
                                    <option value="NEGOTIATED">Negotiated</option>
                                    <option value="DIRECT_CONTRACTING">Direct Contracting</option>
                                </select>
                                @error('procurement_mode')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div id="auto-generate-notice" class="hidden bg-blue-50 border border-blue-200 rounded-md p-4">
                                <p class="text-blue-800 text-sm">
                                    <strong>Note:</strong> Abstract of Quotations and Price Matrix will be automatically generated from supplier quotations.
                                </p>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end space-x-4">
                            <a href="{{ route('purchase-requests.show', $purchaseRequest) }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Create Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('document_type').addEventListener('change', function() {
            const notice = document.getElementById('auto-generate-notice');
            if (this.value === 'ABSTRACT_OF_QUOTATIONS' || this.value === 'PRICE_MATRIX') {
                notice.classList.remove('hidden');
            } else {
                notice.classList.add('hidden');
            }
        });
    </script>
</x-app-layout>

