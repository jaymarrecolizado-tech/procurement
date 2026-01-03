<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Purchase Request Details') }}
            </h2>
            <div class="flex gap-2">
                @if(Auth::user()->hasAnyRole(['END_USER', 'PROCUREMENT_OFFICER', 'ADMIN']) && 
                    ($purchaseRequest->end_user_id == Auth::id() || Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN'])))
                    @if($purchaseRequest->status == 'PR_UNDER_REVIEW')
                        <a href="{{ route('purchase-requests.edit', $purchaseRequest) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                            Edit
                        </a>
                    @endif
                @endif
                @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']))
                    @if(in_array($purchaseRequest->status, ['PR_UNDER_REVIEW', 'RFQ_READY']) && !$purchaseRequest->rfq)
                        <a href="{{ route('rfqs.create', $purchaseRequest) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                            Create RFQ
                        </a>
                    @elseif($purchaseRequest->rfq)
                        <a href="{{ route('rfqs.show', $purchaseRequest->rfq) }}" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            View RFQ
                        </a>
                    @endif
                    <form method="POST" action="{{ route('purchase-requests.update-status', $purchaseRequest) }}" class="inline">
                        @csrf
                        <select name="status" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm">
                            <option value="{{ $purchaseRequest->status }}" selected>{{ $purchaseRequest->status_name }}</option>
                            @foreach(['PR_UNDER_REVIEW', 'RFQ_READY', 'RFQ_DISSEMINATED', 'CANVASS_COMPLETE', 'BAC_DOCS_READY', 'BAC_APPROVED', 'PO_APPROVED', 'PO_COMPLETE', 'COA_STAMPED'] as $status)
                                @if($status != $purchaseRequest->status)
                                    <option value="{{ $status }}">{{ \App\Models\PurchaseRequest::make(['status' => $status])->status_name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </form>
                @endif
                <a href="{{ route('purchase-requests.pdf', $purchaseRequest) }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" target="_blank">
                    Download PDF
                </a>
                <a href="{{ route('purchase-requests.preview', $purchaseRequest) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" target="_blank">
                    Preview PDF
                </a>
                <a href="{{ route('purchase-requests.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">PR Number</h3>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $purchaseRequest->pr_number }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status</h3>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($purchaseRequest->status == 'PR_UNDER_REVIEW') bg-yellow-100 text-yellow-800
                                    @elseif($purchaseRequest->status == 'RFQ_READY' || $purchaseRequest->status == 'RFQ_DISSEMINATED') bg-blue-100 text-blue-800
                                    @elseif($purchaseRequest->status == 'CANVASS_COMPLETE' || $purchaseRequest->status == 'BAC_DOCS_READY') bg-purple-100 text-purple-800
                                    @elseif($purchaseRequest->status == 'BAC_APPROVED' || $purchaseRequest->status == 'PO_APPROVED') bg-green-100 text-green-800
                                    @elseif($purchaseRequest->status == 'COA_STAMPED') bg-indigo-100 text-indigo-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $purchaseRequest->status_name }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Project Title</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $purchaseRequest->project_title }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">End User</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $purchaseRequest->endUser->name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Department</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $purchaseRequest->end_user_department }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Fund Source</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $purchaseRequest->fund_source }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Estimated Budget</h3>
                            <p class="mt-1 text-lg font-semibold text-gray-900">₱{{ number_format($purchaseRequest->estimated_budget, 2) }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Urgency Level</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $purchaseRequest->urgency_level_name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Approval Date</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $purchaseRequest->approval_date->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Created</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $purchaseRequest->created_at->format('F d, Y') }}</p>
                        </div>
                    </div>

                    @if($purchaseRequest->project_description)
                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-500">Project Description</h3>
                            <p class="mt-1 text-gray-900">{{ $purchaseRequest->project_description }}</p>
                        </div>
                    @endif

                    @if($purchaseRequest->urgency_timeline)
                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-500">Urgency Timeline</h3>
                            <p class="mt-1 text-gray-900">{{ $purchaseRequest->urgency_timeline }}</p>
                        </div>
                    @endif

                    <div class="mt-6">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">Required Documents</h3>
                        <div class="flex gap-4">
                            <span class="inline-flex items-center">
                                @if($purchaseRequest->has_signatures)
                                    <svg class="w-5 h-5 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                @endif
                                Signatures
                            </span>
                            <span class="inline-flex items-center">
                                @if($purchaseRequest->has_specs)
                                    <svg class="w-5 h-5 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                @endif
                                Specifications
                            </span>
                            <span class="inline-flex items-center">
                                @if($purchaseRequest->has_quantity)
                                    <svg class="w-5 h-5 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                @endif
                                Quantities
                            </span>
                            <span class="inline-flex items-center">
                                @if($purchaseRequest->has_market_survey)
                                    <svg class="w-5 h-5 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                @else
                                    <svg class="w-5 h-5 text-red-500 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
                                @endif
                                Market Survey
                            </span>
                        </div>
                    </div>

                    @if($purchaseRequest->deficiency_notes)
                        <div class="mt-6">
                            <h3 class="text-sm font-medium text-gray-500">Deficiency Notes</h3>
                            <p class="mt-1 text-gray-900">{{ $purchaseRequest->deficiency_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- PR Items -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">PR Items</h3>
                    @if($purchaseRequest->prItems->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Code</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($purchaseRequest->prItems as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->item_code }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-900">{{ $item->item_name }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $item->item_description ?? '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($item->quantity, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->unit_of_measure }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($item->estimated_price, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($item->total_estimated, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr class="bg-gray-50 font-semibold">
                                        <td colspan="6" class="px-6 py-4 text-right text-sm">Total:</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">₱{{ number_format($purchaseRequest->prItems->sum('total_estimated'), 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No items added to this purchase request.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

