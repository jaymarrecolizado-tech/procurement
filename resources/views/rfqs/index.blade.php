<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">RFQ & Canvass</h2>
                <p class="mt-1 text-sm text-gray-500">Manage request for quotations and canvassing</p>
            </div>
            @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']) && $prsReadyForRfq->count() > 0)
                <div class="flex items-center gap-2 px-4 py-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-medium text-yellow-800">{{ $prsReadyForRfq->count() }} PR(s) ready for RFQ</span>
                </div>
            @endif
        </div>
    </x-slot>

    <div>
        @if(session('success'))
            <x-flash-message type="success" :message="session('success')" />
        @endif

        @if(session('error'))
            <x-flash-message type="error" :message="session('error')" />
        @endif

        <!-- PRs Ready for RFQ Creation -->
        @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']) && $prsReadyForRfq->count() > 0)
            <x-card class="mb-6 border-yellow-200 bg-yellow-50">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Purchase Requests Ready for RFQ</h3>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>PR Number</th>
                                <th>Project Title</th>
                                <th>End User</th>
                                <th>Budget</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($prsReadyForRfq as $pr)
                                <tr>
                                    <td class="font-medium">{{ $pr->pr_number }}</td>
                                    <td>{{ Str::limit($pr->project_title, 40) }}</td>
                                    <td class="text-gray-500">{{ $pr->endUser->name }}</td>
                                    <td class="font-medium">₱{{ number_format($pr->estimated_budget, 2) }}</td>
                                    <td>
                                        <a href="{{ route('rfqs.create', $pr) }}" class="text-indigo-600 hover:text-indigo-700 font-medium">
                                            Create RFQ →
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        @endif

        <!-- RFQs List -->
        <x-card>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Request for Quotations</h3>
            
            @if($rfqs->count() > 0)
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>RFQ Number</th>
                                <th>PR Number</th>
                                <th>Project Title</th>
                                <th>Deadline</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rfqs as $rfq)
                                <tr>
                                    <td class="font-medium">{{ $rfq->rfq_number }}</td>
                                    <td>{{ $rfq->purchaseRequest->pr_number }}</td>
                                    <td>{{ Str::limit($rfq->purchaseRequest->project_title, 40) }}</td>
                                    <td class="text-gray-500">{{ $rfq->canvassing_deadline->format('M d, Y') }}</td>
                                    <td>
                                        <span class="badge 
                                            @if($rfq->status == 'PENDING') badge-warning
                                            @elseif($rfq->status == 'ACTIVE') badge-info
                                            @else badge-success @endif">
                                            {{ $rfq->status_name }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('rfqs.show', $rfq) }}" class="text-indigo-600 hover:text-indigo-700 font-medium">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $rfqs->links() }}</div>
            @else
                <div class="empty-state">
                    <svg class="empty-state-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="empty-state-title">No RFQs found</h3>
                    <p class="empty-state-description">There are no request for quotations at this time.</p>
                </div>
            @endif
        </x-card>
    </div>
</x-app-layout>
