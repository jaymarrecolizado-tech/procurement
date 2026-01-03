<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Purchase Order: ') . $purchaseOrder->po_number }}
            </h2>
            <div class="flex gap-2">
                @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']))
                    @if(!in_array($purchaseOrder->status, ['DISSEMINATED', 'AWAITING_CONFORME', 'COMPLETE']))
                        <a href="{{ route('purchase-orders.edit', $purchaseOrder) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-sm">
                            Edit
                        </a>
                    @endif
                    <form method="POST" action="{{ route('purchase-orders.update-status', $purchaseOrder) }}" class="inline">
                        @csrf
                        <select name="status" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm">
                            <option value="{{ $purchaseOrder->status }}" selected>{{ $purchaseOrder->status_name }}</option>
                            @foreach(['DRAFT', 'PENDING_APPROVAL', 'APPROVED', 'DISSEMINATED', 'AWAITING_CONFORME', 'COMPLETE'] as $status)
                                @if($status != $purchaseOrder->status)
                                    <option value="{{ $status }}">{{ \App\Models\PurchaseOrder::make(['status' => $status])->status_name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </form>
                @endif
                <a href="{{ route('purchase-orders.pdf', $purchaseOrder) }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm" target="_blank">
                    Download PDF
                </a>
                <a href="{{ route('purchase-orders.preview', $purchaseOrder) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm" target="_blank">
                    Preview PDF
                </a>
                <a href="{{ route('purchase-orders.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">PO Number</h3>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $purchaseOrder->po_number }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($purchaseOrder->status === 'COMPLETE') bg-green-100 text-green-800
                                @elseif($purchaseOrder->status === 'DISSEMINATED' || $purchaseOrder->status === 'AWAITING_CONFORME') bg-blue-100 text-blue-800
                                @elseif($purchaseOrder->status === 'APPROVED') bg-purple-100 text-purple-800
                                @elseif($purchaseOrder->status === 'PENDING_APPROVAL') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $purchaseOrder->status_name }}
                            </span>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Purchase Request</h3>
                            <a href="{{ route('purchase-requests.show', $purchaseOrder->purchaseRequest) }}" class="text-indigo-600 hover:text-indigo-900">
                                {{ $purchaseOrder->purchaseRequest->pr_number }}
                            </a>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Contract Amount</h3>
                            <p class="mt-1 text-lg font-semibold text-gray-900">₱{{ number_format($purchaseOrder->contract_amount, 2) }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Supplier Name</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $purchaseOrder->supplier_name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Supplier Address</h3>
                            <p class="mt-1 text-gray-900">{{ $purchaseOrder->supplier_address ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Supplier Contact</h3>
                            <p class="mt-1 text-gray-900">{{ $purchaseOrder->supplier_contact ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Delivery Deadline</h3>
                            <p class="mt-1 text-gray-900">{{ $purchaseOrder->delivery_deadline->format('F d, Y') }}</p>
                        </div>
                        @if($purchaseOrder->delivery_instructions)
                            <div class="md:col-span-2">
                                <h3 class="text-sm font-medium text-gray-500">Delivery Instructions</h3>
                                <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $purchaseOrder->delivery_instructions }}</p>
                            </div>
                        @endif
                        @if($purchaseOrder->payment_terms)
                            <div class="md:col-span-2">
                                <h3 class="text-sm font-medium text-gray-500">Payment Terms</h3>
                                <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $purchaseOrder->payment_terms }}</p>
                            </div>
                        @endif
                        @if($purchaseOrder->notes)
                            <div class="md:col-span-2">
                                <h3 class="text-sm font-medium text-gray-500">Notes</h3>
                                <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $purchaseOrder->notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- PR Items Reference -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Purchase Request Items</h3>
                    @if($purchaseOrder->purchaseRequest->prItems->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Code</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($purchaseOrder->purchaseRequest->prItems as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->item_code }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item->item_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($item->quantity, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->unit_of_measure }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($item->estimated_price, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($item->total_estimated, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-gray-50 font-semibold">
                                        <td colspan="5" class="px-6 py-4 text-right text-sm">Total:</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">₱{{ number_format($purchaseOrder->purchaseRequest->prItems->sum('total_estimated'), 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No items found.</p>
                    @endif
                </div>
            </div>

            <!-- Documents Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Attached Documents</h3>
                        @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']))
                            <button onclick="showUploadModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Upload Document
                            </button>
                        @endif
                    </div>

                    @php
                        $purchaseOrder->loadMissing('documents.uploader');
                    @endphp

                    @if($purchaseOrder->documents && $purchaseOrder->documents->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Document Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">File Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Size</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Uploaded By</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($purchaseOrder->documents as $document)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $document->document_type_name }}</td>
                                            <td class="px-6 py-4 text-sm font-medium">{{ $document->file_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $document->file_size_human }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $document->uploader->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $document->created_at->format('M d, Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('documents.preview', $document) }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">Preview</a>
                                                    <a href="{{ route('documents.download', $document) }}" class="text-green-600 hover:text-green-900">Download</a>
                                                    @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']))
                                                        <form method="POST" action="{{ route('documents.destroy', $document) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No documents attached.</p>
                    @endif
                </div>
            </div>

            <!-- Upload Document Modal -->
            <div id="uploadModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Document</h3>
                    <form method="POST" action="{{ route('documents.upload-po', $purchaseOrder) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Document Type *</label>
                            <select name="document_type" required class="block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="PO">Purchase Order</option>
                                <option value="CONFORME">Conforme</option>
                                <option value="COA_PACKET">COA Packet</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">File *</label>
                            <input type="file" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                            <p class="mt-1 text-xs text-gray-500">Max size: 10MB. Allowed: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG</p>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button" onclick="hideUploadModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                function showUploadModal() {
                    document.getElementById('uploadModal').classList.remove('hidden');
                }
                function hideUploadModal() {
                    document.getElementById('uploadModal').classList.add('hidden');
                }
            </script>
        </div>
    </div>
</x-app-layout>

