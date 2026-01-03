<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('RFQ Details') }}
            </h2>
            <div class="flex gap-2">
                @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']) && 
                    ($rfq->procurement_officer_id == Auth::id() || Auth::user()->hasRole('ADMIN')))
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

            <!-- Canvasses -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
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

