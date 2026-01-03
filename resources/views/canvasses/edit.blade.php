<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Canvass Task') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium mb-4">RFQ Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-500">RFQ Number</p>
                            <p class="font-semibold">{{ $canvass->rfq->rfq_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">PR Number</p>
                            <p class="font-semibold">{{ $canvass->rfq->purchaseRequest->pr_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Project Title</p>
                            <p class="font-semibold">{{ $canvass->rfq->purchaseRequest->project_title }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-500">Canvasser</p>
                            <p class="font-semibold">{{ $canvass->canvasser->name }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('canvasses.update', $canvass) }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <div>
                                <x-input-label for="task_description" :value="__('Task Description')" />
                                <textarea id="task_description" name="task_description" rows="4" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>{{ old('task_description', $canvass->task_description) }}</textarea>
                                <x-input-error :messages="$errors->get('task_description')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="deadline" :value="__('Deadline')" />
                                <x-text-input id="deadline" class="block mt-1 w-full" type="date" name="deadline" :value="old('deadline', $canvass->deadline->format('Y-m-d'))" required />
                                <x-input-error :messages="$errors->get('deadline')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="status" :value="__('Status')" />
                                <select id="status" name="status" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="PENDING" {{ old('status', $canvass->status) == 'PENDING' ? 'selected' : '' }}>Pending</option>
                                    <option value="IN_PROGRESS" {{ old('status', $canvass->status) == 'IN_PROGRESS' ? 'selected' : '' }}>In Progress</option>
                                    <option value="COMPLETED" {{ old('status', $canvass->status) == 'COMPLETED' ? 'selected' : '' }}>Completed</option>
                                    <option value="OVERDUE" {{ old('status', $canvass->status) == 'OVERDUE' ? 'selected' : '' }}>Overdue</option>
                                </select>
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="notes" :value="__('Notes')" />
                                <textarea id="notes" name="notes" rows="3" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes', $canvass->notes) }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-6 gap-4">
                            <a href="{{ route('canvasses.show', $canvass) }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                                Cancel
                            </a>
                            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded">
                                Update Canvass
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

