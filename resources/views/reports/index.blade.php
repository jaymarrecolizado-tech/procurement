<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Reports & Analytics') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Date Range Filter -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('reports.index') }}" class="flex gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Filter
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Overall Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-500">Total PRs</h3>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($stats['total_prs']) }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-500">Total RFQs</h3>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($stats['total_rfqs']) }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-500">Total BAC Docs</h3>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($stats['total_bac_docs']) }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-500">Total POs</h3>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">{{ number_format($stats['total_pos']) }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-500">Total Budget</h3>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">₱{{ number_format($stats['total_budget'], 2) }}</p>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-sm font-medium text-gray-500">Total Contracts</h3>
                    <p class="mt-2 text-3xl font-semibold text-gray-900">₱{{ number_format($stats['total_contract_amount'], 2) }}</p>
                </div>
            </div>

            <!-- Status Distributions -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <!-- PR Status Distribution -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">PR Status Distribution</h3>
                        <div class="space-y-2">
                            @foreach($prStatusDistribution as $status => $count)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">{{ $status }}</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- RFQ Status Distribution -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">RFQ Status Distribution</h3>
                        <div class="space-y-2">
                            @foreach($rfqStatusDistribution as $status => $count)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">{{ $status }}</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- PO Status Distribution -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">PO Status Distribution</h3>
                        <div class="space-y-2">
                            @foreach($poStatusDistribution as $status => $count)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-600">{{ $status }}</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ $count }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approval Time Statistics -->
            @if($approvalTimes && $approvalTimes->avg_time)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Approval Time Statistics</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Average Time</h4>
                                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($approvalTimes->avg_time, 2) }} hours</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Fastest Approval</h4>
                                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($approvalTimes->min_time, 2) }} hours</p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500">Slowest Approval</h4>
                                <p class="mt-1 text-2xl font-semibold text-gray-900">{{ number_format($approvalTimes->max_time, 2) }} hours</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Top Departments -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Top Departments by PR Count</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PR Count</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Budget</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($topDepartments as $dept)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dept->end_user_department }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $dept->count }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($dept->total_budget, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Top Suppliers -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Top Suppliers by PO Count</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PO Count</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($topSuppliers as $supplier)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $supplier->supplier_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $supplier->count }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($supplier->total_amount, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Monthly Trends -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Monthly Trends (Last 12 Months)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PR Count</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Budget</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($monthlyTrends as $trend)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ date('F Y', strtotime($trend->month . '-01')) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $trend->pr_count }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($trend->total_budget, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Approver Statistics -->
            @if($approverStats->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Top Approvers</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Approver</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Approval Count</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg Time (hours)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($approverStats as $stat)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stat->approver->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $stat->approval_count }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($stat->avg_time, 2) }}</td>
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

