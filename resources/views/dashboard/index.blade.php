<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <x-flash-message type="success" :message="session('success')" />
            @endif

            @if(session('error'))
                <x-flash-message type="error" :message="session('error')" />
            @endif

            <!-- Welcome Message -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 overflow-hidden shadow-lg sm:rounded-lg mb-6">
                <div class="p-6 text-white">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-semibold mb-2">Welcome back, {{ Auth::user()->name }}!</h3>
                            <p class="text-indigo-100">
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Role: <span class="font-medium ml-1">{{ Auth::user()->role_name }}</span>
                        </span>
                        @if(Auth::user()->department)
                            <span class="ml-4 inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Department: <span class="font-medium ml-1">{{ Auth::user()->department }}</span>
                            </span>
                        @endif
                    </p>
                        </div>
                        @if(Auth::user()->hasAnyRole(['BAC_MEMBER', 'BAC_CHAIR', 'PROCUREMENT_OFFICER', 'ADMIN']))
                            @php
                                $pendingCount = \App\Models\ApprovalRouting::where('approver_id', Auth::id())
                                    ->where('status', 'PENDING')
                                    ->count();
                            @endphp
                            <a href="{{ route('approvals.dashboard') }}" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-lg transition">
                                <div class="flex items-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="font-medium">Approval Dashboard</span>
                                    @if($pendingCount > 0)
                                        <span class="bg-red-500 text-white text-xs font-bold px-2 py-1 rounded-full">{{ $pendingCount }}</span>
                                    @endif
                                </div>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @foreach($stats as $key => $value)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow duration-200">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    @switch($key)
                                        @case('total_prs')
                                            <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center shadow-sm">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                            @break
                                        @case('pending_review')
                                            <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center shadow-sm">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            @break
                                        @case('in_progress')
                                            <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center shadow-sm">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                </svg>
                                            </div>
                                            @break
                                        @case('completed')
                                            <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center shadow-sm">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            </div>
                                            @break
                                        @case('pending_approval')
                                            <div class="w-12 h-12 bg-yellow-500 rounded-lg flex items-center justify-center shadow-sm">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            @break
                                        @default
                                            <div class="w-12 h-12 bg-gray-500 rounded-lg flex items-center justify-center shadow-sm">
                                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                                </svg>
                                            </div>
                                    @endswitch
                                </div>
                                <div class="ml-4 flex-1">
                                    <p class="text-sm font-medium text-gray-500 uppercase tracking-wide">
                                        @switch($key)
                                            @case('total_prs')
                                                Total PRs
                                                @break
                                            @case('pending_review')
                                                Pending Review
                                                @break
                                            @case('in_progress')
                                                In Progress
                                                @break
                                            @case('completed')
                                                Completed
                                                @break
                                            @case('active_rfqs')
                                                Active RFQs
                                                @break
                                            @case('total_tasks')
                                                Total Tasks
                                                @break
                                            @case('pending_tasks')
                                                Pending Tasks
                                                @break
                                            @case('approved')
                                                Approved
                                                @break
                                            @case('rejected')
                                                Rejected
                                                @break
                                            @case('pending_approval')
                                                Pending Approval
                                                @break
                                            @default
                                                {{ ucfirst(str_replace('_', ' ', $key)) }}
                                        @endswitch
                                    </p>
                                    <p class="text-3xl font-bold text-gray-900 mt-1">{{ $value }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Workflow Visualization -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Procurement Workflow</h3>
                        <div class="space-y-4">
                            @foreach($workflowSteps as $step)
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-{{ $step['color'] }}-500 rounded-full flex items-center justify-center">
                                            <span class="text-white text-sm font-medium">{{ $step['step'] }}</span>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-gray-900">{{ $step['name'] }}</p>
                                        <p class="text-sm text-gray-500">{{ $step['description'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Pending Tasks -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Pending Tasks</h3>
                        @if($pendingTasks->count() > 0)
                            <div class="space-y-3">
                                @foreach($pendingTasks as $task)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                        <div>
                                            @if(isset($task->rfq))
                                                <p class="text-sm font-medium text-gray-900">
                                                    Canvass Task - PR: {{ $task->rfq->purchaseRequest->pr_number }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $task->rfq->purchaseRequest->project_title }}
                                                </p>
                                            @else
                                                <p class="text-sm font-medium text-gray-900">
                                                    PR: {{ $task->pr_number ?? $task->id }}
                                                </p>
                                                <p class="text-sm text-gray-500">
                                                    {{ $task->project_title ?? 'Purchase Request' }}
                                                </p>
                                            @endif
                                        </div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($task->status ?? 'PENDING' == 'PENDING') bg-yellow-100 text-yellow-800
                                            @elseif($task->status ?? 'PENDING' == 'IN_PROGRESS') bg-blue-100 text-blue-800
                                            @else bg-gray-100 text-gray-800 @endif">
                                            {{ ucfirst($task->status ?? 'Pending') }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No pending tasks at the moment.</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Recent Purchase Requests -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Recent Purchase Requests</h3>
                        @if(Auth::user()->hasAnyRole(['END_USER', 'PROCUREMENT_OFFICER', 'ADMIN']))
                            <a href="#" class="text-sm text-indigo-600 hover:text-indigo-500">
                                View All
                            </a>
                        @endif
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PR Number</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Project Title</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End User</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($recentPRs as $pr)
                                    <tr class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <a href="{{ route('purchase-requests.show', $pr) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ $pr->pr_number }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <a href="{{ route('purchase-requests.show', $pr) }}" class="hover:text-indigo-600 transition">
                                                {{ Str::limit($pr->project_title, 50) }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $pr->endUser->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($pr->status == 'PR_UNDER_REVIEW') bg-yellow-100 text-yellow-800
                                                @elseif($pr->status == 'RFQ_READY') bg-blue-100 text-blue-800
                                                @elseif($pr->status == 'COA_STAMPED') bg-green-100 text-green-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $pr->status_name }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $pr->created_at->format('M d, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>