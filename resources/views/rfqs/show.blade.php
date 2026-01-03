<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('RFQ Details') }}
            </h2>
            <div class="flex gap-2">
                @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']) && 
                    ($rfq->procurement_officer_id == Auth::id() || Auth::user()->hasRole('ADMIN')))
                    @if($rfq->status === 'PENDING' && (!$rfq->approvalRoutings || $rfq->approvalRoutings->count() === 0))
                        <a href="{{ route('rfqs.assign-approvers', $rfq) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                            Assign Approvers
                        </a>
                    @elseif($rfq->status === 'PENDING' && $rfq->approvalRoutings && $rfq->approvalRoutings->count() > 0)
                        <a href="{{ route('rfqs.assign-approvers', $rfq) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            Update Approvers
                        </a>
                    @endif
                    <a href="{{ route('rfqs.edit', $rfq) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                        Edit
                    </a>
                @endif
                <a href="{{ route('rfqs.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Approval Actions for Approvers -->
            @php
                $userRouting = null;
                $canApprove = false;
                if ($rfq->approvalRoutings) {
                    $userRouting = $rfq->approvalRoutings->where('approver_id', Auth::id())->where('status', 'PENDING')->first();
                    if ($userRouting) {
                        $previousComplete = $rfq->approvalRoutings->where('sequence', '<', $userRouting->sequence)->where('status', 'APPROVED')->count();
                        $previousTotal = $rfq->approvalRoutings->where('sequence', '<', $userRouting->sequence)->count();
                        $canApprove = $previousComplete === $previousTotal;
                    }
                }
            @endphp

            @if($userRouting && $canApprove && $rfq->status === 'PENDING')
                <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                    <h4 class="font-medium text-yellow-900 mb-3">Your Approval Required</h4>
                    <div class="flex space-x-2">
                        <form method="POST" action="{{ route('rfqs.approve', $rfq) }}" class="inline">
                            @csrf
                            <div class="flex gap-2">
                                <input type="text" name="comments" placeholder="Optional comments..." class="flex-1 rounded-md border-gray-300 shadow-sm text-sm">
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                                    Approve
                                </button>
                            </div>
                        </form>
                        <button type="button" onclick="showRejectModal()" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium">
                            Reject
                        </button>
                    </div>
                </div>

                <!-- Reject Modal -->
                <div id="rejectModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
                    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Reject RFQ</h3>
                        <form method="POST" action="{{ route('rfqs.reject', $rfq) }}">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Rejection *</label>
                                <textarea name="comments" rows="4" required minlength="10" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Please provide a detailed reason for rejection (minimum 10 characters)"></textarea>
                                @error('comments')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="flex justify-end space-x-2">
                                <button type="button" onclick="hideRejectModal()" class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                    Cancel
                                </button>
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium">
                                    Confirm Rejection
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    function showRejectModal() {
                        document.getElementById('rejectModal').classList.remove('hidden');
                    }
                    function hideRejectModal() {
                        document.getElementById('rejectModal').classList.add('hidden');
                    }
                </script>
            @elseif($userRouting && !$canApprove && $rfq->status === 'PENDING')
                <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-4">
                    <p class="text-blue-800 text-sm">This RFQ is pending approval from previous approvers. You will be able to approve once they have completed their review.</p>
                </div>
            @endif

            <!-- Approval Routing Status -->
            @if($rfq->approvalRoutings && $rfq->approvalRoutings->count() > 0)
                <div class="bg-white border border-gray-200 rounded-md p-4 mb-4">
                    <h4 class="font-medium text-gray-900 mb-3">Approval Routing</h4>
                    <div class="space-y-2">
                        @foreach($rfq->approvalRoutings->sortBy('sequence') as $routing)
                            <div class="flex items-center justify-between p-2 rounded {{ $routing->approver_id === Auth::id() ? 'bg-yellow-50' : '' }}">
                                <div class="flex items-center space-x-3">
                                    <span class="text-sm font-medium text-gray-700">#{{ $routing->sequence }}</span>
                                    <span class="text-sm text-gray-900">{{ $routing->approver->name ?? 'N/A' }}</span>
                                    <span class="text-xs text-gray-500">({{ $routing->approver->role_name ?? $routing->approver_role }})</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                        @if($routing->status === 'APPROVED') bg-green-100 text-green-800
                                        @elseif($routing->status === 'REJECTED') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif">
                                        {{ $routing->status_name }}
                                    </span>
                                    @if($routing->signed_at)
                                        <span class="text-xs text-gray-500">{{ $routing->signed_at->format('M d, Y') }}</span>
                                    @endif
                                </div>
                            </div>
                            @if($routing->comments)
                                <div class="ml-8 text-sm text-gray-600 italic">"{{ $routing->comments }}"</div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- RFQ Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">RFQ Number</h3>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $rfq->rfq_number }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status</h3>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($rfq->status == 'PENDING') bg-yellow-100 text-yellow-800
                                    @elseif($rfq->status == 'ACTIVE') bg-blue-100 text-blue-800
                                    @else bg-green-100 text-green-800 @endif">
                                    {{ $rfq->status_name }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">PR Number</h3>
                            <p class="mt-1 text-lg text-gray-900">
                                <a href="{{ route('purchase-requests.show', $rfq->purchaseRequest) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $rfq->purchaseRequest->pr_number }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Project Title</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $rfq->purchaseRequest->project_title }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Canvassing Deadline</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $rfq->canvassing_deadline->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Procurement Officer</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $rfq->procurementOfficer->name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Created</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $rfq->created_at->format('F d, Y') }}</p>
                        </div>
                    </div>

                    @if($rfq->delivery_schedule)
                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-500">Delivery Schedule</h3>
                            <p class="mt-1 text-gray-900">{{ $rfq->delivery_schedule }}</p>
                        </div>
                    @endif

                    @if($rfq->payment_terms)
                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-500">Payment Terms</h3>
                            <p class="mt-1 text-gray-900">{{ $rfq->payment_terms }}</p>
                        </div>
                    @endif

                    @if($rfq->notes)
                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-500">Notes</h3>
                            <p class="mt-1 text-gray-900">{{ $rfq->notes }}</p>
                        </div>
                    @endif

                    @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']))
                        <div class="mt-6">
                            <form method="POST" action="{{ route('rfqs.update-status', $rfq) }}" class="inline">
                                @csrf
                                <x-input-label for="status" :value="__('Update Status')" />
                                <select name="status" onchange="this.form.submit()" class="mt-1 rounded-md border-gray-300 shadow-sm">
                                    <option value="{{ $rfq->status }}" selected>{{ $rfq->status_name }}</option>
                                    @foreach(['PENDING', 'ACTIVE', 'COMPLETED'] as $status)
                                        @if($status != $rfq->status)
                                            <option value="{{ $status }}">{{ \App\Models\RFQ::make(['status' => $status])->status_name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- PR Items -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">PR Items</h3>
                    @if($rfq->purchaseRequest->prItems->count() > 0)
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
                                    @foreach($rfq->purchaseRequest->prItems as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->item_code }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item->item_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($item->quantity, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->unit_of_measure }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($item->estimated_price, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($item->total_estimated, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Documents Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Attached Documents</h3>
                        @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']) && 
                            ($rfq->procurement_officer_id == Auth::id() || Auth::user()->hasRole('ADMIN')))
                            <button onclick="showUploadModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm">
                                Upload Document
                            </button>
                        @endif
                    </div>

                    @php
                        $rfq->loadMissing('documents.uploader');
                    @endphp

                    @if($rfq->documents && $rfq->documents->count() > 0)
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
                                    @foreach($rfq->documents as $document)
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
                                                    @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']) && 
                                                        ($rfq->procurement_officer_id == Auth::id() || Auth::user()->hasRole('ADMIN')))
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
                    <form method="POST" action="{{ route('documents.upload-rfq', $rfq) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Document Type *</label>
                            <select name="document_type" required class="block w-full rounded-md border-gray-300 shadow-sm">
                                <option value="RFQ">Request for Quotation</option>
                                <option value="QUOTATION">Quotation</option>
                                <option value="AOQ">Abstract of Quotations</option>
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

            <!-- Canvasses -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Canvassing Tasks</h3>
                        @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']))
                            <a href="{{ route('canvasses.create', $rfq) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Assign Canvasser
                            </a>
                        @endif
                    </div>
                    @if($rfq->canvasses->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Canvasser</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deadline</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quotations</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($rfq->canvasses as $canvass)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $canvass->canvasser->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $canvass->deadline->format('M d, Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($canvass->status == 'PENDING') bg-yellow-100 text-yellow-800
                                                    @elseif($canvass->status == 'IN_PROGRESS') bg-blue-100 text-blue-800
                                                    @elseif($canvass->status == 'COMPLETED') bg-green-100 text-green-800
                                                    @else bg-red-100 text-red-800 @endif">
                                                    {{ $canvass->status_name }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $canvass->supplierQuotations->count() }} quotation(s)
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="{{ route('canvasses.show', $canvass) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500">No canvassing tasks assigned yet.</p>
                            @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']))
                                <a href="{{ route('canvasses.create', $rfq) }}" class="mt-4 inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                    Assign First Canvasser
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

