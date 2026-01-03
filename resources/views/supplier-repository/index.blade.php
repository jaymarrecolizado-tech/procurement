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

            <!-- Tabs -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <a href="{{ route('supplier-repository.index', ['tab' => 'suppliers'] + request()->except('quotations_page')) }}" 
                       class="{{ $activeTab === 'suppliers' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Suppliers ({{ $suppliers->total() }})
                    </a>
                    <a href="{{ route('supplier-repository.index', ['tab' => 'quotations'] + request()->except('suppliers_page')) }}" 
                       class="{{ $activeTab === 'quotations' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Quotation History ({{ $quotations->total() }})
                    </a>
                </nav>
            </div>

            @if($activeTab === 'suppliers')
                <!-- Suppliers Tab -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                    <form method="GET" action="{{ route('supplier-repository.index', ['tab' => 'suppliers']) }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <!-- Enhanced Supplier Search -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Search Suppliers
                                    <span class="text-xs text-gray-500 font-normal">(Name, Email, Contact, Address, Items, RFQ #)</span>
                                </label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                        </svg>
                                    </div>
                                    <input type="text" 
                                           name="supplier_search" 
                                           value="{{ request('supplier_search') }}" 
                                           placeholder="Search by name, email, contact, address, items quoted, RFQ number... (supports multiple words)"
                                           class="w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                           autocomplete="off">
                                    @if(request('supplier_search'))
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <button type="button" 
                                                    onclick="document.querySelector('input[name=supplier_search]').value=''; this.form.submit();"
                                                    class="text-gray-400 hover:text-gray-600">
                                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    💡 Tip: Use multiple words to search across different fields (e.g., "computer laptop")
                                </p>
                            </div>

                            <!-- Category Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                <select name="supplier_category" class="w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">All Categories</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat }}" {{ request('supplier_category') == $cat ? 'selected' : '' }}>
                                            {{ $cat }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Status Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="supplier_status" class="w-full rounded-md border-gray-300 shadow-sm">
                                    <option value="">All Status</option>
                                    <option value="ACTIVE" {{ request('supplier_status') == 'ACTIVE' ? 'selected' : '' }}>Active</option>
                                    <option value="INACTIVE" {{ request('supplier_status') == 'INACTIVE' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Search
                            </button>
                            <a href="{{ route('supplier-repository.index', ['tab' => 'suppliers']) }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
                                Clear
                            </a>
                        </div>
                    </form>
                </div>

                <!-- Suppliers Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        @if(request('supplier_search') || request('supplier_category') || request('supplier_status'))
                            <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-md">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm text-blue-800">
                                        <strong>{{ $suppliers->total() }}</strong> supplier(s) found
                                        @if(request('supplier_search'))
                                            <span class="ml-2">for "<strong>{{ request('supplier_search') }}</strong>"</span>
                                        @endif
                                    </div>
                                    <a href="{{ route('supplier-repository.index', ['tab' => 'suppliers']) }}" 
                                       class="text-xs text-blue-600 hover:text-blue-800 underline">
                                        Clear all filters
                                    </a>
                                </div>
                            </div>
                        @endif
                        @if($suppliers->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier Name</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Address</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quotations</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @php
                                            $searchTerm = request('supplier_search', '');
                                            $highlight = function($text, $search) {
                                                if (empty($search) || empty($text)) return $text;
                                                $terms = preg_split('/\s+/', trim($search));
                                                $highlighted = $text;
                                                foreach ($terms as $term) {
                                                    $term = trim($term);
                                                    if (!empty($term)) {
                                                        $highlighted = preg_replace('/(' . preg_quote($term, '/') . ')/i', '<mark class="bg-yellow-200 px-1 rounded">$1</mark>', $highlighted);
                                                    }
                                                }
                                                return $highlighted;
                                            };
                                        @endphp
                                        @foreach($suppliers as $supplier)
                                            <tr class="hover:bg-gray-50">
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{!! $highlight($supplier->supplier_name_original, $searchTerm) !!}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {!! $highlight($supplier->supplier_category ?? '-', $searchTerm) !!}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {!! $highlight($supplier->supplier_contact ?? '-', $searchTerm) !!}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {!! $highlight($supplier->supplier_email ?? '-', $searchTerm) !!}
                                                </td>
                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                    {!! $highlight(Str::limit($supplier->supplier_address ?? '-', 50), $searchTerm) !!}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <span class="font-semibold">{{ $supplier->quotation_history_count }}</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        {{ $supplier->status === 'ACTIVE' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                        {{ $supplier->status_name }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {{ $supplier->created_at->format('M d, Y') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <div class="mt-4">
                                {{ $suppliers->links() }}
                            </div>
                        @else
                            <div class="text-center py-12">
                                <p class="text-gray-500 text-lg">No suppliers found.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <!-- Quotations Tab -->
                <!-- Search and Filters -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6 p-6">
                    <form method="GET" action="{{ route('supplier-repository.index', ['tab' => 'quotations']) }}" class="space-y-4">
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
                                    @foreach($allSuppliers as $supplier)
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

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                            <!-- Per Page Selector -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Items Per Page</label>
                                <select name="per_page" onchange="this.form.submit()" class="w-full rounded-md border-gray-300 shadow-sm">
                                    @php
                                        $currentPerPage = (int) request('per_page', 10);
                                    @endphp
                                    <option value="10" {{ $currentPerPage == 10 ? 'selected' : '' }}>10 per page</option>
                                    <option value="20" {{ $currentPerPage == 20 ? 'selected' : '' }}>20 per page</option>
                                    <option value="30" {{ $currentPerPage == 30 ? 'selected' : '' }}>30 per page</option>
                                    <option value="50" {{ $currentPerPage == 50 ? 'selected' : '' }}>50 per page</option>
                                    <option value="100" {{ $currentPerPage == 100 ? 'selected' : '' }}>100 per page</option>
                                </select>
                            </div>
                        </div>


                        <div class="flex gap-2">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                Search
                            </button>
                            <a href="{{ route('supplier-repository.index', ['tab' => 'quotations']) }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
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
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
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
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($quotations as $quotation)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                    <div class="flex space-x-2">
                                                        <a href="{{ route('supplier-repository.show', $quotation) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                                        @if(Auth::user()->hasAnyRole(['CANVASSER', 'PROCUREMENT_OFFICER', 'ADMIN']))
                                                            <a href="{{ route('supplier-repository.edit', $quotation) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                                        @endif
                                                    </div>
                                                </td>
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
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        <!-- Pagination -->
                        <div class="mt-4 flex items-center justify-between">
                            <div class="text-sm text-gray-700">
                                Showing {{ $quotations->firstItem() }} to {{ $quotations->lastItem() }} of {{ $quotations->total() }} results
                            </div>
                            <div>
                                {{ $quotations->links() }}
                            </div>
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
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        // Enhanced search UX
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('input[name="supplier_search"]');
            if (searchInput) {
                // Auto-focus on search input when tab is active
                if (window.location.search.includes('tab=suppliers')) {
                    searchInput.focus();
                }
                
                // Clear button functionality
                const clearBtn = searchInput.parentElement.querySelector('button');
                if (clearBtn) {
                    clearBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        searchInput.value = '';
                        searchInput.form.submit();
                    });
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
