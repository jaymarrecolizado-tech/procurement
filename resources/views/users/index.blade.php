<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Management') }}
            </h2>
            <a href="{{ route('users.create') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm">
                Create User
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="GET" action="{{ route('users.index') }}" class="mb-4 flex gap-2">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, email, or department..." class="flex-1 rounded-md border-gray-300 shadow-sm">
                        <select name="role" class="rounded-md border-gray-300 shadow-sm">
                            <option value="">All Roles</option>
                            @foreach(['END_USER', 'PROCUREMENT_OFFICER', 'BAC_SECRETARIAT', 'BAC_CHAIR', 'BAC_MEMBER', 'CANVASSER', 'SUPPLIER', 'ADMIN'] as $role)
                                <option value="{{ $role }}" {{ request('role') === $role ? 'selected' : '' }}>
                                    {{ \App\Models\User::make(['role' => $role])->role_name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">Filter</button>
                        @if(request('search') || request('role'))
                            <a href="{{ route('users.index') }}" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">Clear</a>
                        @endif
                    </form>
                    
                    @if($users->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Role</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($users as $user)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $user->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->email }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium
                                                    @if($user->role === 'ADMIN') bg-red-100 text-red-800
                                                    @elseif($user->role === 'PROCUREMENT_OFFICER') bg-blue-100 text-blue-800
                                                    @elseif($user->role === 'BAC_CHAIR' || $user->role === 'BAC_SECRETARIAT') bg-purple-100 text-purple-800
                                                    @else bg-gray-100 text-gray-800
                                                    @endif">
                                                    {{ $user->role_name }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->department ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $user->created_at->format('M d, Y') }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <div class="flex items-center space-x-3">
                                                    <a href="{{ route('users.show', $user) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                                    @if($user->id !== Auth::id())
                                                        <a href="{{ route('users.edit', $user) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                                                        <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">{{ $users->links() }}</div>
                    @else
                        <p class="text-gray-500">No users found.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

