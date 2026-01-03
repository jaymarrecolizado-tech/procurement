<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('BAC Document: ') . $bacDocument->document_type_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('purchase-requests.show', $bacDocument->purchaseRequest) }}" class="text-indigo-600 hover:text-indigo-900">
                    ← Back to Purchase Request
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Document Type</h3>
                            <p class="text-lg font-semibold text-gray-900">{{ $bacDocument->document_type_name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($bacDocument->status === 'APPROVED') bg-green-100 text-green-800
                                @elseif($bacDocument->status === 'REJECTED') bg-red-100 text-red-800
                                @elseif($bacDocument->status === 'PENDING_APPROVAL') bg-yellow-100 text-yellow-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $bacDocument->status_name }}
                            </span>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Procurement Mode</h3>
                            <p class="text-lg text-gray-900">{{ $bacDocument->procurement_mode_name ?? 'Not specified' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Purchase Request</h3>
                            <a href="{{ route('purchase-requests.show', $bacDocument->purchaseRequest) }}" class="text-indigo-600 hover:text-indigo-900">
                                {{ $bacDocument->purchaseRequest->pr_number }}
                            </a>
                        </div>
                    </div>

                    @if(Auth::user()->hasAnyRole(['BAC_SECRETARIAT', 'PROCUREMENT_OFFICER', 'ADMIN']) && $bacDocument->status !== 'APPROVED')
                        <div class="flex space-x-2 mb-4">
                            <a href="{{ route('bac-documents.edit', $bacDocument) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm font-medium">
                                Edit Document
                            </a>
                            @if($bacDocument->status === 'DRAFT' && (!$bacDocument->approvalRoutings || $bacDocument->approvalRoutings->count() === 0))
                                <a href="{{ route('bac-documents.assign-approvers', $bacDocument) }}" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 text-sm font-medium">
                                    Assign Approvers
                                </a>
                            @elseif($bacDocument->status === 'DRAFT' && $bacDocument->approvalRoutings && $bacDocument->approvalRoutings->count() > 0)
                                <a href="{{ route('bac-documents.assign-approvers', $bacDocument) }}" class="px-4 py-2 bg-yellow-600 text-white rounded-md hover:bg-yellow-700 text-sm font-medium">
                                    Update Approvers
                                </a>
                            @endif
                            <form method="POST" action="{{ route('bac-documents.destroy', $bacDocument) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this document?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm font-medium">
                                    Delete
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- Approval Actions for Approvers -->
                    @php
                        $userRouting = null;
                        $canApprove = false;
                        if ($bacDocument->approvalRoutings) {
                            $userRouting = $bacDocument->approvalRoutings->where('approver_id', Auth::id())->where('status', 'PENDING')->first();
                            if ($userRouting) {
                                $previousComplete = $bacDocument->approvalRoutings->where('sequence', '<', $userRouting->sequence)->where('status', 'APPROVED')->count();
                                $previousTotal = $bacDocument->approvalRoutings->where('sequence', '<', $userRouting->sequence)->count();
                                $canApprove = $previousComplete === $previousTotal;
                            }
                        }
                    @endphp

                    @if($userRouting && $canApprove && $bacDocument->status === 'PENDING_APPROVAL')
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-4 mb-4">
                            <h4 class="font-medium text-yellow-900 mb-3">Your Approval Required</h4>
                            <div class="flex space-x-2">
                                <form method="POST" action="{{ route('bac-documents.approve', $bacDocument) }}" class="inline">
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
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Document</h3>
                                <form method="POST" action="{{ route('bac-documents.reject', $bacDocument) }}">
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
                    @elseif($userRouting && !$canApprove && $bacDocument->status === 'PENDING_APPROVAL')
                        <div class="bg-blue-50 border border-blue-200 rounded-md p-4 mb-4">
                            <p class="text-blue-800 text-sm">This document is pending approval from previous approvers. You will be able to approve once they have completed their review.</p>
                        </div>
                    @endif

                    <!-- Approval Routing Status -->
                    @if($bacDocument->approvalRoutings && $bacDocument->approvalRoutings->count() > 0)
                        <div class="bg-white border border-gray-200 rounded-md p-4 mb-4">
                            <h4 class="font-medium text-gray-900 mb-3">Approval Routing</h4>
                            <div class="space-y-2">
                                @foreach($bacDocument->approvalRoutings->sortBy('sequence') as $routing)
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
                </div>
            </div>

            <!-- Document Content -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Document Content</h3>

                    @if($bacDocument->document_type === 'ABSTRACT_OF_QUOTATIONS')
                        @include('bac-documents.partials.abstract-of-quotations', ['content' => $bacDocument->content, 'supplierQuotations' => $supplierQuotations])
                    @elseif($bacDocument->document_type === 'PRICE_MATRIX')
                        @include('bac-documents.partials.price-matrix', ['content' => $bacDocument->content, 'purchaseRequest' => $bacDocument->purchaseRequest])
                    @else
                        <div class="bg-gray-50 rounded-md p-4">
                            <pre class="whitespace-pre-wrap text-sm">{{ json_encode($bacDocument->content, JSON_PRETTY_PRINT) }}</pre>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

