<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('User Details: ') . $user->name }}
            </h2>
            <div class="flex gap-2">
                @if($user->id !== Auth::id())
                    <a href="{{ route('users.edit', $user) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded text-sm">
                        Edit
                    </a>
                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded text-sm">
                            Delete
                        </button>
                    </form>
                @else
                    <a href="{{ route('profile.edit') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded text-sm">
                        Edit Profile
                    </a>
                @endif
                <a href="{{ route('users.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded text-sm">
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

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Name</h3>
                            <p class="mt-1 text-lg font-semibold text-gray-900">{{ $user->name }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Email</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $user->email }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Role</h3>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                @if($user->role === 'ADMIN') bg-red-100 text-red-800
                                @elseif($user->role === 'PROCUREMENT_OFFICER') bg-blue-100 text-blue-800
                                @elseif($user->role === 'BAC_CHAIR' || $user->role === 'BAC_SECRETARIAT') bg-purple-100 text-purple-800
                                @else bg-gray-100 text-gray-800
                                @endif">
                                {{ $user->role_name }}
                            </span>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Department</h3>
                            <p class="mt-1 text-lg text-gray-900">{{ $user->department ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Account Created</h3>
                            <p class="mt-1 text-gray-900">{{ $user->created_at->format('F d, Y g:i A') }}</p>
                        </div>
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Last Updated</h3>
                            <p class="mt-1 text-gray-900">{{ $user->updated_at->format('F d, Y g:i A') }}</p>
                        </div>
                    </div>

                    @if($user->id !== Auth::id())
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <h3 class="text-lg font-medium mb-4">Reset Password</h3>
                            <form method="POST" action="{{ route('users.reset-password', $user) }}">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700">New Password *</label>
                                        <input type="password" name="password" id="password" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        @error('password')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password *</label>
                                        <input type="password" name="password_confirmation" id="password_confirmation" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    </div>
                                    <div class="flex items-end">
                                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                                            Reset Password
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Activity Logs -->
            @if($activityLogs->count() > 0)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium mb-4">Recent Activity</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Entity</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Performed By</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($activityLogs as $log)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">{{ $log->action }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $log->entity_type }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $log->user->name ?? 'System' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $log->created_at->format('M d, Y g:i A') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

