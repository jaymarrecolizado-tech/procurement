<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Pending Approvals') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <p class="text-gray-600 mb-4">BAC Documents pending your approval.</p>
                    
                    @if($pendingApprovals->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Document Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PR Number</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Project Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sequence</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pending Since</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pendingApprovals as $routing)
                                        @php
                                            $bacDocument = $routing->bacDocument;
                                            $pr = $bacDocument->purchaseRequest;
                                            
                                            // Check if previous approvals are complete
                                            $previousComplete = $bacDocument->approvalRoutings
                                                ->where('sequence', '<', $routing->sequence)
                                                ->where('status', 'APPROVED')
                                                ->count();
                                            
                                            $previousTotal = $bacDocument->approvalRoutings
                                                ->where('sequence', '<', $routing->sequence)
                                                ->count();
                                            
                                            $canApprove = $previousComplete === $previousTotal;
                                        @endphp
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $bacDocument->document_type_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="{{ route('purchase-requests.show', $pr) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $pr->pr_number }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 text-sm">{{ $pr->project_title }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    #{{ $routing->sequence }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $routing->created_at->diffForHumans() }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($canApprove)
                                                    <div class="flex items-center space-x-2">
                                                        <a href="{{ route('bac-documents.show', $bacDocument) }}" class="text-indigo-600 hover:text-indigo-900">
                                                            Review
                                                        </a>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 text-xs">Waiting for previous approvers</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $pendingApprovals->links() }}</div>
                    @else
                        <p class="text-gray-500">No pending approvals at this time.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

