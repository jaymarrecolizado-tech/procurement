<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Approval Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Pending</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_pending'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Ready to Approve</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['can_approve_now'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Waiting for Others</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['waiting_for_others'] }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-100 rounded-md p-3">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Approved</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_approved'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('approvals.dashboard') }}" class="flex gap-2">
                        <select name="type" class="rounded-md border-gray-300 shadow-sm">
                            <option value="all" {{ $documentType === 'all' ? 'selected' : '' }}>All Document Types</option>
                            <option value="PR" {{ $documentType === 'PR' ? 'selected' : '' }}>Purchase Requests</option>
                            <option value="RFQ" {{ $documentType === 'RFQ' ? 'selected' : '' }}>RFQs</option>
                            <option value="BAC" {{ $documentType === 'BAC' ? 'selected' : '' }}>BAC Documents</option>
                        </select>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">Filter</button>
                        @if($documentType !== 'all')
                            <a href="{{ route('approvals.dashboard') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">Clear</a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Pending Approvals -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pending Approvals</h3>
                    
                    @if($allPendingApprovals->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Document</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PR Number</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sequence</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pending Since</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($allPendingApprovals as $approval)
                                        <tr class="{{ $approval['can_approve'] ? 'bg-yellow-50' : '' }}">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($approval['type'] === 'PR') bg-blue-100 text-blue-800
                                                    @elseif($approval['type'] === 'RFQ') bg-indigo-100 text-indigo-800
                                                    @else bg-purple-100 text-purple-800
                                                    @endif">
                                                    {{ $approval['type'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $approval['document_type'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $approval['pr_number'] }}</td>
                                            <td class="px-6 py-4 text-sm">{{ $approval['project_title'] }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    #{{ $approval['sequence'] }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($approval['can_approve'])
                                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Ready
                                                    </span>
                                                @else
                                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                        Waiting
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $approval['pending_since']->diffForHumans() }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="{{ $approval['url'] }}" class="text-indigo-600 hover:text-indigo-900">
                                                    Review
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No pending approvals at this time.</p>
                    @endif
                </div>
            </div>

            <!-- Approval History -->
            @if($approvalHistory->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Approval History</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Document Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PR Number</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Decision</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Comments</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($approvalHistory as $history)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($history->document_type === 'PR')
                                                    Purchase Request
                                                @elseif($history->document_type === 'RFQ')
                                                    Request for Quotation
                                                @else
                                                    {{ $history->bacDocument->document_type_name ?? 'N/A' }}
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($history->document_type === 'PR')
                                                    <a href="{{ route('purchase-requests.show', $history->purchaseRequest) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ $history->purchaseRequest->pr_number ?? 'N/A' }}
                                                    </a>
                                                @elseif($history->document_type === 'RFQ')
                                                    <a href="{{ route('purchase-requests.show', $history->rfq->purchaseRequest) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ $history->rfq->purchaseRequest->pr_number ?? 'N/A' }}
                                                    </a>
                                                @else
                                                    <a href="{{ route('purchase-requests.show', $history->bacDocument->purchaseRequest) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ $history->bacDocument->purchaseRequest->pr_number ?? 'N/A' }}
                                                    </a>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($history->status === 'APPROVED') bg-green-100 text-green-800
                                                    @else bg-red-100 text-red-800
                                                    @endif">
                                                    {{ $history->status_name }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $history->signed_at ? $history->signed_at->format('M d, Y g:i A') : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-500">
                                                {{ $history->comments ? Str::limit($history->comments, 50) : '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

