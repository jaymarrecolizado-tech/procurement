<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create RFQ') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">Purchase Request Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">PR Number</p>
                            <p class="font-semibold">{{ $purchaseRequest->pr_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Project Title</p>
                            <p class="font-semibold">{{ $purchaseRequest->project_title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Estimated Budget</p>
                            <p class="font-semibold">₱{{ number_format($purchaseRequest->estimated_budget, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">End User</p>
                            <p class="font-semibold">{{ $purchaseRequest->endUser->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('rfqs.store', $purchaseRequest) }}">
                        @csrf

                        <div class="space-y-6">
                            <div>
                                <x-input-label for="canvassing_deadline" :value="__('Canvassing Deadline')" />
                                <x-text-input id="canvassing_deadline" class="block mt-1 w-full" type="date" name="canvassing_deadline" :value="old('canvassing_deadline')" required />
                                <x-input-error :messages="$errors->get('canvassing_deadline')" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-500">Deadline for suppliers to submit quotations</p>
                            </div>

                            <div>
                                <x-input-label for="delivery_schedule" :value="__('Delivery Schedule')" />
                                <textarea id="delivery_schedule" name="delivery_schedule" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('delivery_schedule') }}</textarea>
                                <x-input-error :messages="$errors->get('delivery_schedule')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="payment_terms" :value="__('Payment Terms')" />
                                <textarea id="payment_terms" name="payment_terms" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('payment_terms') }}</textarea>
                                <x-input-error :messages="$errors->get('payment_terms')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="notes" :value="__('Notes')" />
                                <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 gap-4">
                            <a href="{{ route('rfqs.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Create RFQ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

