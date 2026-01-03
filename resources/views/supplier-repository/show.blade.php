<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Quotation History Details') }}
            </h2>
            <div class="flex gap-2">
                @if(Auth::user()->hasAnyRole(['CANVASSER', 'PROCUREMENT_OFFICER', 'ADMIN']))
                    <a href="{{ route('supplier-repository.edit', $quotationHistory) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                        Edit
                    </a>
                @endif
                <a href="{{ route('supplier-repository.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Supplier Name</p>
                            <p class="font-semibold text-gray-900">{{ $quotationHistory->supplier->supplier_name_original }}</p>
                        </div>
                        @if($quotationHistory->supplier->supplier_category)
                            <div>
                                <p class="text-sm text-gray-500">Category</p>
                                <p class="font-semibold text-gray-900">{{ $quotationHistory->supplier->supplier_category }}</p>
                            </div>
                        @endif
                        @if($quotationHistory->supplier->supplier_address)
                            <div>
                                <p class="text-sm text-gray-500">Address</p>
                                <p class="text-gray-900">{{ $quotationHistory->supplier->supplier_address }}</p>
                            </div>
                        @endif
                        @if($quotationHistory->supplier->supplier_contact)
                            <div>
                                <p class="text-sm text-gray-500">Contact</p>
                                <p class="text-gray-900">{{ $quotationHistory->supplier->supplier_contact }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Item Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Item Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Item Name</p>
                            <p class="font-semibold text-gray-900">{{ $quotationHistory->item_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Item Code</p>
                            <p class="font-semibold text-gray-900">{{ $quotationHistory->item_code }}</p>
                        </div>
                        @if($quotationHistory->item_description)
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-500">Description</p>
                                <p class="text-gray-900">{{ $quotationHistory->item_description }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-500">Quantity</p>
                            <p class="font-semibold text-gray-900">{{ number_format($quotationHistory->quantity, 2) }} {{ $quotationHistory->unit_of_measure }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Unit Price</p>
                            <p class="font-semibold text-gray-900">₱{{ number_format($quotationHistory->unit_price, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Total Price</p>
                            <p class="font-semibold text-lg text-gray-900">₱{{ number_format($quotationHistory->total_price, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quotation Details -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Quotation Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">Quotation Date</p>
                            <p class="font-semibold text-gray-900">{{ $quotationHistory->quotation_date->format('F d, Y') }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Quotation Age</p>
                            <p class="font-semibold text-gray-900">{{ $quotationHistory->quotation_age }} old</p>
                        </div>
                        @if($quotationHistory->rfq_number)
                            <div>
                                <p class="text-sm text-gray-500">RFQ Number</p>
                                <p class="font-semibold text-gray-900">{{ $quotationHistory->rfq_number }}</p>
                            </div>
                        @endif
                        @if($quotationHistory->validity_period)
                            <div>
                                <p class="text-sm text-gray-500">Validity Period</p>
                                <p class="font-semibold text-gray-900">{{ $quotationHistory->validity_period->format('F d, Y') }}</p>
                            </div>
                        @endif
                        @if($quotationHistory->delivery_days)
                            <div>
                                <p class="text-sm text-gray-500">Delivery Days</p>
                                <p class="font-semibold text-gray-900">{{ $quotationHistory->delivery_days }} days</p>
                            </div>
                        @endif
                        @if($quotationHistory->payment_terms)
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-500">Payment Terms</p>
                                <p class="text-gray-900">{{ $quotationHistory->payment_terms }}</p>
                            </div>
                        @endif
                        @if($quotationHistory->remarks)
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-500">Remarks</p>
                                <p class="text-gray-900">{{ $quotationHistory->remarks }}</p>
                            </div>
                        @endif
                        <div>
                            <p class="text-sm text-gray-500">Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $quotationHistory->is_selected ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $quotationHistory->is_selected ? 'Selected' : 'Not Selected' }}
                            </span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Entered By</p>
                            <p class="font-semibold text-gray-900">{{ $quotationHistory->enteredBy->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quotation Images -->
            @if($quotationHistory->images->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Quotation Images ({{ $quotationHistory->images->count() }})</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            @foreach($quotationHistory->images as $image)
                                <div class="border rounded-md p-2">
                                    <a href="{{ Storage::url($image->image_path) }}" target="_blank">
                                        @if(str_contains($image->mime_type, 'image'))
                                            <img src="{{ Storage::url($image->image_path) }}" alt="Quotation Image" class="w-full h-48 object-cover rounded cursor-pointer hover:opacity-75">
                                        @else
                                            <div class="w-full h-48 bg-gray-100 rounded flex items-center justify-center">
                                                <span class="text-gray-600">PDF Document</span>
                                            </div>
                                        @endif
                                    </a>
                                    <p class="text-xs text-gray-500 mt-2 truncate">{{ $image->original_filename }}</p>
                                    <p class="text-xs text-gray-400">{{ $image->file_size_human }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Activity Log -->
            @if($quotationHistory->activityLogs->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Activity History</h3>
                        <div class="space-y-3">
                            @foreach($quotationHistory->activityLogs->sortByDesc('created_at') as $log)
                                <div class="border-l-4 {{ $log->action == 'CREATED' ? 'border-green-500' : ($log->action == 'UPDATED' ? 'border-yellow-500' : 'border-red-500') }} pl-4 py-2">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $log->action_name }}
                                                @if($log->field_name)
                                                    - Field: <span class="font-normal">{{ $log->field_name }}</span>
                                                @endif
                                            </p>
                                            @if($log->field_name && $log->old_value !== null && $log->new_value !== null)
                                                <p class="text-xs text-gray-600 mt-1">
                                                    Changed from: <span class="line-through">{{ $log->old_value }}</span> 
                                                    to: <span class="font-semibold">{{ $log->new_value }}</span>
                                                </p>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">{{ $log->changedBy->name ?? 'N/A' }}</p>
                                            <p class="text-xs text-gray-400">{{ $log->created_at->format('M d, Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

