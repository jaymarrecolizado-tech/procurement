<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Supplier Quotation Repository') }}
            </h2>
            @if(Auth::user()->hasAnyRole(['CANVASSER', 'PROCUREMENT_OFFICER', 'ADMIN']))
                <a href="{{ route('supplier-repository.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                    Add Quotation
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Search and Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                <form method="GET" action="{{ route('supplier-repository.index') }}" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Wildcard Search -->
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Search (Wildcard)</label>
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="Search supplier, item, RFQ number, description..."
                                   class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        </div>

                        <!-- Supplier Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Supplier</label>
                            <select name="supplier_id" class="w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">All Suppliers</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                                        {{ $supplier->supplier_name_original }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select name="category" class="w-full rounded-md border-gray-300 shadow-sm">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Date Range -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                            <input type="date" name="end_date" value="{{ request('end_date') }}" 
                                   class="w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Search
                        </button>
                        <a href="{{ route('supplier-repository.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                            Clear
                        </a>
                    </div>
                </form>
            </div>

            <!-- Quotation History List -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($quotations->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Item Code</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Age</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">RFQ</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Images</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($quotations as $quotation)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <div class="font-medium text-gray-900">{{ $quotation->supplier->supplier_name_original }}</div>
                                                @if($quotation->supplier->supplier_category)
                                                    <div class="text-xs text-gray-500">{{ $quotation->supplier->supplier_category }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-sm">
                                                <div class="font-medium text-gray-900">{{ $quotation->item_name }}</div>
                                                @if($quotation->item_description)
                                                    <div class="text-xs text-gray-500">{{ Str::limit($quotation->item_description, 50) }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $quotation->item_code }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ number_format($quotation->quantity, 2) }} {{ $quotation->unit_of_measure }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₱{{ number_format($quotation->unit_price, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">₱{{ number_format($quotation->total_price, 2) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $quotation->quotation_date->format('M d, Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $quotation->quotation_age }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @if($quotation->rfq_number)
                                                    {{ $quotation->rfq_number }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($quotation->images->count() > 0)
                                                    <span class="text-indigo-600">{{ $quotation->images->count() }} image(s)</span>
                                                @else
                                                    <span class="text-gray-400">No images</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <div class="flex space-x-2">
                                                    <a href="{{ route('supplier-repository.show', $quotation) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                                    @if(Auth::user()->hasAnyRole(['CANVASSER', 'PROCUREMENT_OFFICER', 'ADMIN']))
                                                        <a href="{{ route('supplier-repository.edit', $quotation) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
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
                            {{ $quotations->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500 text-lg">No quotation history found.</p>
                            @if(Auth::user()->hasAnyRole(['CANVASSER', 'PROCUREMENT_OFFICER', 'ADMIN']))
                                <a href="{{ route('supplier-repository.create') }}" class="mt-4 inline-block bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
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

