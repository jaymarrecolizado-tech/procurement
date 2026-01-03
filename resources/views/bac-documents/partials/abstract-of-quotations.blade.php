<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier Name</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Address</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quote Price</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Delivery Days</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Compliant</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($supplierQuotations as $quotation)
                <tr class="{{ $quotation->is_selected ? 'bg-green-50' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        {{ $quotation->supplier_name }}
                        @if($quotation->is_selected)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                Selected
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $quotation->supplier_address ?? 'N/A' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $quotation->supplier_contact ?? 'N/A' }}
                        @if($quotation->supplier_email)
                            <br><span class="text-xs">{{ $quotation->supplier_email }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        ₱{{ number_format($quotation->quote_price, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $quotation->delivery_days ?? 'N/A' }} days
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        Submitted: {{ $quotation->submitted_date->format('M d, Y') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                        No supplier quotations found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

