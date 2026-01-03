<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Purchase Requests') }}
            </h2>
            @if(Auth::user()->hasAnyRole(['END_USER', 'PROCUREMENT_OFFICER', 'ADMIN']))
                <a href="{{ route('purchase-requests.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Purchase Request
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <x-flash-message type="success" :message="session('success')" />
            @endif

            @if(session('error'))
                <x-flash-message type="error" :message="session('error')" />
            @endif

            @if(session('warning'))
                <x-flash-message type="warning" :message="session('warning')" />
            @endif

            @if(session('info'))
                <x-flash-message type="info" :message="session('info')" />
            @endif

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                <form method="GET" action="{{ route('purchase-requests.index') }}" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label for="search" class="sr-only">Search</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>
                            <input type="text" id="search" name="search" value="{{ request('search') }}" 
                                   placeholder="Search by PR number, title, or description..."
                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>
                    </div>
                    <div class="w-full sm:w-auto">
                        <label for="status" class="sr-only">Status</label>
                        <select id="status" name="status" class="block w-full sm:w-48 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm py-2">
                            <option value="">All Statuses</option>
                            <option value="PR_UNDER_REVIEW" {{ request('status') == 'PR_UNDER_REVIEW' ? 'selected' : '' }}>PR Under Review</option>
                            <option value="RFQ_READY" {{ request('status') == 'RFQ_READY' ? 'selected' : '' }}>RFQ Ready</option>
                            <option value="RFQ_DISSEMINATED" {{ request('status') == 'RFQ_DISSEMINATED' ? 'selected' : '' }}>RFQ Disseminated</option>
                            <option value="CANVASS_COMPLETE" {{ request('status') == 'CANVASS_COMPLETE' ? 'selected' : '' }}>Canvass Complete</option>
                            <option value="BAC_DOCS_READY" {{ request('status') == 'BAC_DOCS_READY' ? 'selected' : '' }}>BAC Docs Ready</option>
                            <option value="BAC_APPROVED" {{ request('status') == 'BAC_APPROVED' ? 'selected' : '' }}>BAC Approved</option>
                            <option value="PO_APPROVED" {{ request('status') == 'PO_APPROVED' ? 'selected' : '' }}>PO Approved</option>
                            <option value="PO_COMPLETE" {{ request('status') == 'PO_COMPLETE' ? 'selected' : '' }}>PO Complete</option>
                            <option value="COA_STAMPED" {{ request('status') == 'COA_STAMPED' ? 'selected' : '' }}>COA Stamped</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                            </svg>
                            Filter
                        </button>
                        @if(request('search') || request('status'))
                            <a href="{{ route('purchase-requests.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Purchase Requests Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($purchaseRequests->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PR Number</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project Title</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End User</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Budget</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($purchaseRequests as $pr)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $pr->pr_number }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <div class="max-w-xs truncate" title="{{ $pr->project_title }}">
                                                    {{ $pr->project_title }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $pr->endUser->name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                ₱{{ number_format($pr->estimated_budget, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($pr->status == 'PR_UNDER_REVIEW') bg-yellow-100 text-yellow-800
                                                    @elseif($pr->status == 'RFQ_READY' || $pr->status == 'RFQ_DISSEMINATED') bg-blue-100 text-blue-800
                                                    @elseif($pr->status == 'CANVASS_COMPLETE' || $pr->status == 'BAC_DOCS_READY') bg-purple-100 text-purple-800
                                                    @elseif($pr->status == 'BAC_APPROVED' || $pr->status == 'PO_APPROVED') bg-green-100 text-green-800
                                                    @elseif($pr->status == 'COA_STAMPED') bg-indigo-100 text-indigo-800
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ $pr->status_name }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $pr->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center space-x-3">
                                                    <a href="{{ route('purchase-requests.show', $pr) }}" class="text-indigo-600 hover:text-indigo-900 font-medium transition">View</a>
                                                    @if(Auth::user()->hasAnyRole(['END_USER', 'PROCUREMENT_OFFICER', 'ADMIN']) && 
                                                        ($pr->end_user_id == Auth::id() || Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])))
                                                        @if($pr->status == 'PR_UNDER_REVIEW')
                                                            <a href="{{ route('purchase-requests.edit', $pr) }}" class="text-yellow-600 hover:text-yellow-900 font-medium transition">Edit</a>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $purchaseRequests->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No purchase requests</h3>
                            <p class="mt-1 text-sm text-gray-500">Get started by creating a new purchase request.</p>
                            @if(Auth::user()->hasAnyRole(['END_USER', 'PROCUREMENT_OFFICER', 'ADMIN']))
                                <div class="mt-6">
                                    <a href="{{ route('purchase-requests.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                        </svg>
                                        Create Your First Purchase Request
                                    </a>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

