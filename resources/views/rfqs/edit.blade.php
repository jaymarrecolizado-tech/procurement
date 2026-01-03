<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit RFQ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('rfqs.update', $rfq) }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <div>
                                <x-input-label for="canvassing_deadline" :value="__('Canvassing Deadline')" />
                                <x-text-input id="canvassing_deadline" class="block mt-1 w-full" type="date" name="canvassing_deadline" :value="old('canvassing_deadline', $rfq->canvassing_deadline->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('canvassing_deadline')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="PENDING" {{ old('status', $rfq->status) == 'PENDING' ? 'selected' : '' }}>Pending</option>
                                    <option value="ACTIVE" {{ old('status', $rfq->status) == 'ACTIVE' ? 'selected' : '' }}>Active</option>
                                    <option value="COMPLETED" {{ old('status', $rfq->status) == 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="delivery_schedule" :value="__('Delivery Schedule')" />
                                <textarea id="delivery_schedule" name="delivery_schedule" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('delivery_schedule', $rfq->delivery_schedule) }}</textarea>
                                <x-input-error :messages="$errors->get('delivery_schedule')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="payment_terms" :value="__('Payment Terms')" />
                                <textarea id="payment_terms" name="payment_terms" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('payment_terms', $rfq->payment_terms) }}</textarea>
                                <x-input-error :messages="$errors->get('payment_terms')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="notes" :value="__('Notes')" />
                                <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $rfq->notes) }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 gap-4">
                            <a href="{{ route('rfqs.show', $rfq) }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Update RFQ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

