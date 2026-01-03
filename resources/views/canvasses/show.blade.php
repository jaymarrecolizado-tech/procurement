<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Canvass Task Details') }}
            </h2>
            <div class="flex gap-2">
                @if(Auth::user()->hasAnyRole(['CANVASSER', 'PROCUREMENT_OFFICER', 'ADMIN']) && 
                    ($canvass->canvasser_id == Auth::id() || Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])))
                    <a href="{{ route('canvasses.edit', $canvass) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 shadow-sm transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                @endif
                <a href="{{ route('canvasses.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back
                </a>
            </div>
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

            <!-- Canvass Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">RFQ Number</h3>
                            <p class="mt-1 text-lg font-semibold text-gray-900">
                                <a href="{{ route('rfqs.show', $canvass->rfq) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $canvass->rfq->rfq_number }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">PR Number</h3>
                            <p class="mt-1 text-lg font-semibold text-gray-900">
                                <a href="{{ route('purchase-requests.show', $canvass->rfq->purchaseRequest) }}" class="text-indigo-600 hover:text-indigo-900">
                                    {{ $canvass->rfq->purchaseRequest->pr_number }}
                                </a>
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Project Title</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $canvass->rfq->purchaseRequest->project_title }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Canvasser</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $canvass->canvasser->name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Deadline</h3>
                            <p class="mt-1 text-lg text-gray-900 {{ $canvass->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                                {{ $canvass->deadline->format('F d, Y') }}
                                @if($canvass->isOverdue())
                                    <span class="ml-2 text-sm">(Overdue)</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status</h3>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($canvass->status == 'PENDING') bg-yellow-100 text-yellow-800
                                    @elseif($canvass->status == 'IN_PROGRESS') bg-blue-100 text-blue-800
                                    @elseif($canvass->status == 'COMPLETED') bg-green-100 text-green-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $canvass->status_name }}
                                </span>
                            </p>
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-500">Task Description</h3>
                        <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $canvass->task_description }}</p>
                    </div>

                    @if($canvass->notes)
                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-500">Notes</h3>
                            <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $canvass->notes }}</p>
                        </div>
                    @endif

                    @if(Auth::user()->hasAnyRole(['CANVASSER', 'PROCUREMENT_OFFICER', 'ADMIN']))
                        <div class="mt-6">
                            <form method="POST" action="{{ route('canvasses.update-status', $canvass) }}" class="inline">
                                @csrf
                                <x-input-label for="status" :value="__('Update Status')" />
                                <select name="status" onchange="this.form.submit()" class="mt-1 rounded-md border-gray-300 shadow-sm">
                                    <option value="{{ $canvass->status }}" selected>{{ $canvass->status_name }}</option>
                                    @foreach(['PENDING', 'IN_PROGRESS', 'COMPLETED', 'OVERDUE'] as $status)
                                        @if($status != $canvass->status)
                                            <option value="{{ $status }}">{{ \App\Models\Canvass::make(['status' => $status])->status_name }}</option>
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
                    <h3 class="text-lg font-medium mb-4">Items to Canvass</h3>
                    @if($canvass->rfq->purchaseRequest->prItems->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Code</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Estimated Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($canvass->rfq->purchaseRequest->prItems as $item)
                                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->item_code }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <div class="font-medium">{{ $item->item_name }}</div>
                                                @if($item->item_description)
                                                    <div class="text-xs text-gray-500 mt-1">{{ $item->item_description }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($item->quantity, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->unit_of_measure }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($item->estimated_price, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">₱{{ number_format($item->total_estimated, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Supplier Quotations -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium">Supplier Quotations</h3>
                        @if(Auth::user()->hasAnyRole(['CANVASSER', 'PROCUREMENT_OFFICER', 'ADMIN']) && 
                            ($canvass->canvasser_id == Auth::id() || Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])))
                            <a href="{{ route('supplier-quotations.create', $canvass) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Add Quotation
                            </a>
                        @endif
                    </div>

                    @if($canvass->supplierQuotations->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quote Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Delivery Days</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Compliant</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($canvass->supplierQuotations as $quotation)
                                        <tr class="{{ $quotation->is_selected ? 'bg-green-50' : 'hover:bg-gray-50' }} transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <div class="font-medium">{{ $quotation->supplier_name }}</div>
                                                @if($quotation->supplier_contact)
                                                    <div class="text-xs text-gray-500 mt-1">{{ $quotation->supplier_contact }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                                ₱{{ number_format($quotation->quote_price, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $quotation->delivery_days ? $quotation->delivery_days . ' days' : 'N/A' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($quotation->is_compliant)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        Yes
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                        No
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($quotation->is_selected)
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                        Selected
                                                    </span>
                                                @else
                                                    <span class="text-sm text-gray-500">Pending</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex items-center space-x-3">
                                                    <a href="{{ route('supplier-quotations.show', $quotation) }}" class="text-indigo-600 hover:text-indigo-900 transition">View</a>
                                                    @if(Auth::user()->hasAnyRole(['CANVASSER', 'PROCUREMENT_OFFICER', 'ADMIN']) && 
                                                        ($canvass->canvasser_id == Auth::id() || Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])))
                                                        <a href="{{ route('supplier-quotations.edit', $quotation) }}" class="text-yellow-600 hover:text-yellow-900 transition">Edit</a>
                                                    @endif
                                                    @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']) && !$quotation->is_selected)
                                                        <form method="POST" action="{{ route('supplier-quotations.select', $quotation) }}" class="inline">
                                                            @csrf
                                                            <button type="submit" class="text-green-600 hover:text-green-900 transition">Select</button>
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
                        <div class="text-center py-8">
                            <p class="text-gray-500">No supplier quotations yet.</p>
                            @if(Auth::user()->hasAnyRole(['CANVASSER', 'PROCUREMENT_OFFICER', 'ADMIN']) && 
                                ($canvass->canvasser_id == Auth::id() || Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])))
                                <a href="{{ route('supplier-quotations.create', $canvass) }}" class="mt-4 inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                    Add First Quotation
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

