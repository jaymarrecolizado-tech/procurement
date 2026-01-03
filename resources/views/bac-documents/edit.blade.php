<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit BAC Document: ') . $bacDocument->document_type_name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4">
                        <a href="{{ route('bac-documents.show', $bacDocument) }}" class="text-indigo-600 hover:text-indigo-900">
                            ← Back to Document
                        </a>
                    </div>

                    <form method="POST" action="{{ route('bac-documents.update', $bacDocument) }}">
                        @csrf
                        @method('PUT')

                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Document Type</label>
                                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $bacDocument->document_type_name }}</p>
                                <p class="mt-1 text-sm text-gray-500">Document type cannot be changed.</p>
                            </div>

                            <div>
                                <label for="procurement_mode" class="block text-sm font-medium text-gray-700">Procurement Mode</label>
                                <select name="procurement_mode" id="procurement_mode" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Select Procurement Mode</option>
                                    <option value="SHOPPING" {{ $bacDocument->procurement_mode === 'SHOPPING' ? 'selected' : '' }}>Shopping</option>
                                    <option value="SVP" {{ $bacDocument->procurement_mode === 'SVP' ? 'selected' : '' }}>Small Value Procurement (SVP)</option>
                                    <option value="PUBLIC_BIDDING" {{ $bacDocument->procurement_mode === 'PUBLIC_BIDDING' ? 'selected' : '' }}>Public Bidding</option>
                                    <option value="NEGOTIATED" {{ $bacDocument->procurement_mode === 'NEGOTIATED' ? 'selected' : '' }}>Negotiated</option>
                                    <option value="DIRECT_CONTRACTING" {{ $bacDocument->procurement_mode === 'DIRECT_CONTRACTING' ? 'selected' : '' }}>Direct Contracting</option>
                                </select>
                                @error('procurement_mode')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            @if(in_array($bacDocument->document_type, ['ABSTRACT_OF_QUOTATIONS', 'PRICE_MATRIX']))
                                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                                    <p class="text-blue-800 text-sm mb-2">
                                        <strong>Note:</strong> This document is auto-generated from supplier quotations.
                                    </p>
                                    <label class="flex items-center">
                                        <input type="checkbox" name="regenerate" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        <span class="ml-2 text-sm text-blue-800">Regenerate from latest supplier quotations</span>
                                    </label>
                                </div>
                            @else
                                <div>
                                    <label for="content" class="block text-sm font-medium text-gray-700">Content (JSON)</label>
                                    <textarea name="content" id="content" rows="15" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 font-mono text-sm">{{ json_encode($bacDocument->content, JSON_PRETTY_PRINT) }}</textarea>
                                    <p class="mt-1 text-sm text-gray-500">Enter valid JSON format for document content.</p>
                                    @error('content')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endif
                        </div>

                        <div class="mt-6 flex items-center justify-end space-x-4">
                            <a href="{{ route('bac-documents.show', $bacDocument) }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Update Document
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

