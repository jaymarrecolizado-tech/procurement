<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Assign Approvers: ') . $bacDocument->document_type_name }}
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

                    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-md p-4">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Document Information</h3>
                        <p class="text-sm text-gray-600">Document Type: <strong>{{ $bacDocument->document_type_name }}</strong></p>
                        <p class="text-sm text-gray-600">PR Number: <strong>{{ $bacDocument->purchaseRequest->pr_number }}</strong></p>
                        <p class="text-sm text-gray-600">Status: <strong>{{ $bacDocument->status_name }}</strong></p>
                    </div>

                    @if($existingRoutings->count() > 0)
                        <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                            <h4 class="font-medium text-yellow-900 mb-2">Existing Approval Routing</h4>
                            <div class="space-y-2">
                                @foreach($existingRoutings as $routing)
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm text-yellow-800">
                                            {{ $routing->sequence }}. {{ $routing->approver->name }} ({{ $routing->approver->role_name }}) - 
                                            <span class="font-medium">{{ $routing->status_name }}</span>
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-yellow-700 mt-2">Assigning new approvers will replace the existing routing.</p>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('bac-documents.store-approvers', $bacDocument) }}" id="approversForm">
                        @csrf

                        <div id="approvers-container" class="space-y-4">
                            <!-- Approver fields will be added here dynamically -->
                        </div>

                        <div class="mt-4">
                            <button type="button" id="add-approver-btn" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                                + Add Approver
                            </button>
                        </div>

                        <div class="mt-6 flex items-center justify-end space-x-4">
                            <a href="{{ route('bac-documents.show', $bacDocument) }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                                Cancel
                            </a>
                            <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                                Assign Approvers
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let approverCount = 0;
        const bacMembers = @json($bacMembers->map(function($u) { return ['id' => $u->id, 'name' => $u->name, 'role' => $u->role_name]; }));

        function addApproverField() {
            const container = document.getElementById('approvers-container');
            const approverDiv = document.createElement('div');
            approverDiv.className = 'flex gap-4 items-end border-b pb-4';
            
            let optionsHtml = '<option value="">Select Approver</option>';
            bacMembers.forEach(function(member) {
                optionsHtml += '<option value="' + member.id + '">' + member.name + ' (' + member.role + ')</option>';
            });
            
            approverDiv.innerHTML = 
                '<div class="flex-1">' +
                    '<label class="block text-sm font-medium text-gray-700">Approver</label>' +
                    '<select name="approvers[' + approverCount + '][approver_id]" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">' +
                        optionsHtml +
                    '</select>' +
                '</div>' +
                '<div class="w-32">' +
                    '<label class="block text-sm font-medium text-gray-700">Sequence</label>' +
                    '<input type="number" name="approvers[' + approverCount + '][sequence]" value="' + (approverCount + 1) + '" min="1" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">' +
                '</div>' +
                '<div>' +
                    '<button type="button" class="px-3 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 text-sm remove-approver">Remove</button>' +
                '</div>';
            
            container.appendChild(approverDiv);
            approverCount++;

            // Add remove functionality
            approverDiv.querySelector('.remove-approver').addEventListener('click', function() {
                approverDiv.remove();
                updateSequences();
            });
        }

        function updateSequences() {
            const containers = document.querySelectorAll('#approvers-container > div');
            containers.forEach(function(container, index) {
                const sequenceInput = container.querySelector('input[name*="[sequence]"]');
                if (sequenceInput) {
                    sequenceInput.value = index + 1;
                    // Update the name attribute to match new index
                    const nameAttr = sequenceInput.getAttribute('name');
                    const newName = nameAttr.replace(/approvers\[\d+\]/, 'approvers[' + index + ']');
                    sequenceInput.setAttribute('name', newName);
                    
                    const select = container.querySelector('select[name*="[approver_id]"]');
                    if (select) {
                        const selectName = select.getAttribute('name');
                        const newSelectName = selectName.replace(/approvers\[\d+\]/, 'approvers[' + index + ']');
                        select.setAttribute('name', newSelectName);
                    }
                }
            });
        }

        document.getElementById('add-approver-btn').addEventListener('click', addApproverField);

        // Add initial approver field
        addApproverField();
    </script>
</x-app-layout>

