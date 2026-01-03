@php
    $prItems = $purchaseRequest->prItems;
    $suppliers = $content['suppliers'] ?? [];
    $items = $content['items'] ?? [];
@endphp

<div class="overflow-x-auto">
    <div class="mb-4">
        <h4 class="text-sm font-medium text-gray-700 mb-2">PR Items Comparison</h4>
    </div>
    
    <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border-b">Item Code</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border-b">Item Name</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border-b">Qty</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase border-b">Unit</th>
                @foreach($suppliers as $supplier)
                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase border-b">
                        {{ $supplier['name'] }}
                        @if($supplier['is_selected'] ?? false)
                            <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                ✓
                            </span>
                        @endif
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($items as $item)
                <tr>
                    <td class="px-4 py-3 text-sm font-medium border-b">{{ $item['item_code'] ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-sm border-b">{{ $item['item_name'] ?? 'N/A' }}</td>
                    <td class="px-4 py-3 text-sm text-center border-b">{{ number_format($item['quantity'] ?? 0, 2) }}</td>
                    <td class="px-4 py-3 text-sm text-center border-b">{{ $item['unit_of_measure'] ?? 'N/A' }}</td>
                    @foreach($item['prices'] ?? [] as $price)
                        <td class="px-4 py-3 text-sm text-center border-b">
                            @if($price['unit_price'] !== null)
                                <div>₱{{ number_format($price['unit_price'], 2) }}</div>
                                <div class="text-xs text-gray-500">Total: ₱{{ number_format($price['total_price'] ?? 0, 2) }}</div>
                            @else
                                <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ 4 + count($suppliers) }}" class="px-6 py-4 text-center text-sm text-gray-500">
                        No items found.
                    </td>
                </tr>
            @endforelse
            <tr class="bg-gray-50 font-semibold">
                <td colspan="4" class="px-4 py-3 text-sm text-right border-b">Total:</td>
                @foreach($content['totals'] ?? [] as $total)
                    <td class="px-4 py-3 text-sm text-center font-medium border-b">
                        ₱{{ number_format($total['total'], 2) }}
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>
</div>

