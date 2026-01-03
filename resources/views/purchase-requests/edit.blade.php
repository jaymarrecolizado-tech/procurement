<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Purchase Request') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('purchase-requests.update', $purchaseRequest) }}" id="prForm">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium mb-4">Basic Information</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="project_title" :value="__('Project Title')" />
                                    <x-text-input id="project_title" class="block mt-1 w-full" type="text" name="project_title" :value="old('project_title', $purchaseRequest->project_title)" required />
                                    <x-input-error :messages="$errors->get('project_title')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="end_user_department" :value="__('Department')" />
                                    <x-text-input id="end_user_department" class="block mt-1 w-full" type="text" name="end_user_department" :value="old('end_user_department', $purchaseRequest->end_user_department)" required />
                                    <x-input-error :messages="$errors->get('end_user_department')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="fund_source" :value="__('Fund Source')" />
                                    <x-text-input id="fund_source" class="block mt-1 w-full" type="text" name="fund_source" :value="old('fund_source', $purchaseRequest->fund_source)" required />
                                    <x-input-error :messages="$errors->get('fund_source')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="estimated_budget" :value="__('Estimated Budget')" />
                                    <x-text-input id="estimated_budget" class="block mt-1 w-full" type="number" step="0.01" name="estimated_budget" :value="old('estimated_budget', $purchaseRequest->estimated_budget)" required />
                                    <x-input-error :messages="$errors->get('estimated_budget')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="urgency_level" :value="__('Urgency Level')" />
                                    <select id="urgency_level" name="urgency_level" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                        <option value="LOW" {{ old('urgency_level', $purchaseRequest->urgency_level) == 'LOW' ? 'selected' : '' }}>Low</option>
                                        <option value="MEDIUM" {{ old('urgency_level', $purchaseRequest->urgency_level) == 'MEDIUM' ? 'selected' : '' }}>Medium</option>
                                        <option value="HIGH" {{ old('urgency_level', $purchaseRequest->urgency_level) == 'HIGH' ? 'selected' : '' }}>High</option>
                                        <option value="URGENT" {{ old('urgency_level', $purchaseRequest->urgency_level) == 'URGENT' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('urgency_level')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="approval_date" :value="__('Approval Date')" />
                                    <x-text-input id="approval_date" class="block mt-1 w-full" type="date" name="approval_date" :value="old('approval_date', $purchaseRequest->approval_date->format('Y-m-d'))" required />
                                    <x-input-error :messages="$errors->get('approval_date')" class="mt-2" />
                                </div>
                            </div>

                            <div class="mt-6">
                                <x-input-label for="project_description" :value="__('Project Description')" />
                                <textarea id="project_description" name="project_description" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('project_description', $purchaseRequest->project_description) }}</textarea>
                                <x-input-error :messages="$errors->get('project_description')" class="mt-2" />
                            </div>

                            <div class="mt-6">
                                <x-input-label for="urgency_timeline" :value="__('Urgency Timeline (Optional)')" />
                                    <x-text-input id="urgency_timeline" class="block mt-1 w-full" type="text" name="urgency_timeline" :value="old('urgency_timeline', $purchaseRequest->urgency_timeline)" placeholder="e.g., Within 30 days" />
                                <x-input-error :messages="$errors->get('urgency_timeline')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Required Documents -->
                        <div class="mb-8">
                            <h3 class="text-lg font-medium mb-4">Required Documents</h3>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_signatures" value="1" {{ old('has_signatures', $purchaseRequest->has_signatures) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600">Signatures</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_specs" value="1" {{ old('has_specs', $purchaseRequest->has_specs) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600">Specifications</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_quantity" value="1" {{ old('has_quantity', $purchaseRequest->has_quantity) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600">Quantities</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_market_survey" value="1" {{ old('has_market_survey', $purchaseRequest->has_market_survey) ? 'checked' : '' }} class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600">Market Survey</span>
                                </label>
                            </div>
                        </div>

                        <!-- PR Items -->
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium">PR Items</h3>
                                <button type="button" onclick="addItem()" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                                    Add Item
                                </button>
                            </div>
                            <div id="items-container">
                                <!-- Items will be added here dynamically -->
                            </div>
                        </div>

                        <!-- Deficiency Notes -->
                        <div class="mb-8">
                            <x-input-label for="deficiency_notes" :value="__('Deficiency Notes (Optional)')" />
                            <textarea id="deficiency_notes" name="deficiency_notes" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('deficiency_notes', $purchaseRequest->deficiency_notes) }}</textarea>
                            <x-input-error :messages="$errors->get('deficiency_notes')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('purchase-requests.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Update Purchase Request') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let itemCount = 0;
        
        function addItem() {
            const container = document.getElementById('items-container');
            const itemHtml = `
                <div class="item-row border border-gray-200 p-4 rounded mb-4" data-item-index="${itemCount}">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="font-medium">Item ${itemCount + 1}</h4>
                        <button type="button" onclick="removeItem(${itemCount})" class="text-red-600 hover:text-red-800 text-sm">Remove</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Item Code</label>
                            <input type="text" name="items[${itemCount}][item_code]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Item Name</label>
                            <input type="text" name="items[${itemCount}][item_name]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div class="md:col-span-3">
                            <label class="block text-sm font-medium text-gray-700">Description</label>
                            <textarea name="items[${itemCount}][item_description]" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Quantity</label>
                            <input type="number" step="0.01" name="items[${itemCount}][quantity]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Unit of Measure</label>
                            <input type="text" name="items[${itemCount}][unit_of_measure]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estimated Price</label>
                            <input type="number" step="0.01" name="items[${itemCount}][estimated_price]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', itemHtml);
            itemCount++;
        }

        function removeItem(index) {
            const item = document.querySelector(`[data-item-index="${index}"]`);
            if (item) {
                item.remove();
            }
        }

        // Load existing items
        document.addEventListener('DOMContentLoaded', function() {
            @if($purchaseRequest->prItems->count() > 0)
                @foreach($purchaseRequest->prItems as $index => $item)
                    addItem();
                    const itemRow = document.querySelectorAll('.item-row')[{{ $index }}];
                    itemRow.querySelector('input[name*="[item_code]"]').value = '{{ $item->item_code }}';
                    itemRow.querySelector('input[name*="[item_name]"]').value = '{{ $item->item_name }}';
                    itemRow.querySelector('textarea[name*="[item_description]"]').value = '{{ $item->item_description ?? '' }}';
                    itemRow.querySelector('input[name*="[quantity]"]').value = '{{ $item->quantity }}';
                    itemRow.querySelector('input[name*="[unit_of_measure]"]').value = '{{ $item->unit_of_measure }}';
                    itemRow.querySelector('input[name*="[estimated_price]"]').value = '{{ $item->estimated_price }}';
                @endforeach
            @else
                addItem();
            @endif
        });
    </script>
</x-app-layout>

