<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Supplier Quotation Details') }}
            </h2>
            <div class="flex gap-2">
                @if(Auth::user()->hasAnyRole(['CANVASSER', 'PROCUREMENT_OFFICER', 'ADMIN']))
                    <a href="{{ route('supplier-quotations.edit', $supplierQuotation) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                        Edit
                    </a>
                @endif
                <a href="{{ route('canvasses.show', $supplierQuotation->canvass) }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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

            <!-- Supplier Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Supplier Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Supplier Name</h3>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $supplierQuotation->supplier_name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Contact Number</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $supplierQuotation->supplier_contact ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Email</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $supplierQuotation->supplier_email ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Submitted Date</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $supplierQuotation->submitted_date->format('F d, Y') }}</p>
                        </div>
                        @if($supplierQuotation->supplier_address)
                            <div class="md:col-span-2">
                                <h3 class="text-sm font-medium text-gray-500">Address</h3>
                                <p class="mt-1 text-lg text-gray-900">{{ $supplierQuotation->supplier_address }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quotation Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Quotation Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Quote Price</h3>
                            <p class="mt-1 text-2xl font-bold text-indigo-600">₱{{ number_format($supplierQuotation->quote_price, 2) }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Delivery Days</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $supplierQuotation->delivery_days ? $supplierQuotation->delivery_days . ' days' : 'N/A' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Compliance</h3>
                            <p class="mt-1">
                                @if($supplierQuotation->is_compliant)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Compliant
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Not Compliant
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Status</h3>
                            <p class="mt-1">
                                @if($supplierQuotation->is_selected)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                        Selected
                                    </span>
                                @else
                                    <span class="text-sm text-gray-500">Pending Selection</span>
                                @endif
                            </p>
                        </div>
                        @if($supplierQuotation->remarks)
                            <div class="md:col-span-2">
                                <h3 class="text-sm font-medium text-gray-500">Remarks</h3>
                                <p class="mt-1 text-gray-900 whitespace-pre-wrap">{{ $supplierQuotation->remarks }}</p>
                            </div>
                        @endif
                    </div>

                    @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']) && !$supplierQuotation->is_selected)
                        <div class="mt-6">
                            <form method="POST" action="{{ route('supplier-quotations.select', $supplierQuotation) }}" class="inline">
                                @csrf
                                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                    Select This Quotation
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quotation Items -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Quotation Items</h3>
                    @if($supplierQuotation->quotationItems->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($supplierQuotation->quotationItems as $item)
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-900">
                                                <div>{{ $item->item_name }}</div>
                                                @if($item->item_description)
                                                    <div class="text-xs text-gray-500">{{ $item->item_description }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ number_format($item->quantity, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $item->unit_of_measure }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($item->unit_price, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($item->total_price, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-right text-sm font-medium text-gray-900">Total:</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-indigo-600">₱{{ number_format($supplierQuotation->quote_price, 2) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500">No items found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

