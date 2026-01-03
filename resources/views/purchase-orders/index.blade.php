<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Purchase Orders') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4 flex justify-between items-center">
                        <p class="text-gray-600">Purchase Orders and completed procurement transactions.</p>
                        <form method="GET" action="{{ route('purchase-orders.index') }}" class="flex gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..." class="rounded-md border-gray-300 shadow-sm">
                            <select name="status" class="rounded-md border-gray-300 shadow-sm">
                                <option value="">All Status</option>
                                @foreach(['DRAFT', 'PENDING_APPROVAL', 'APPROVED', 'DISSEMINATED', 'AWAITING_CONFORME', 'COMPLETE'] as $status)
                                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                                        {{ \App\Models\PurchaseOrder::make(['status' => $status])->status_name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">Filter</button>
                            @if(request('search') || request('status'))
                                <a href="{{ route('purchase-orders.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">Clear</a>
                            @endif
                        </form>
                    </div>
                    
                    @if($purchaseOrders->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PO Number</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">PR Number</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contract Amount</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($purchaseOrders as $po)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $po->po_number }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="{{ route('purchase-requests.show', $po->purchaseRequest) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $po->purchaseRequest->pr_number }}
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 text-sm">{{ $po->supplier_name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">₱{{ number_format($po->contract_amount, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($po->status === 'COMPLETE') bg-green-100 text-green-800
                                                    @elseif($po->status === 'DISSEMINATED' || $po->status === 'AWAITING_CONFORME') bg-blue-100 text-blue-800
                                                    @elseif($po->status === 'APPROVED') bg-purple-100 text-purple-800
                                                    @elseif($po->status === 'PENDING_APPROVAL') bg-yellow-100 text-yellow-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ $po->status_name }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <a href="{{ route('purchase-orders.show', $po) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $purchaseOrders->links() }}</div>
                    @else
                        <p class="text-gray-500">No purchase orders available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

