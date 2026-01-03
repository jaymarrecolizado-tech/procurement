<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Error Messages -->
                    @if($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <ul class="list-disc list-inside">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Profile Information -->
                    <div class="mb-8">
                        <h3 class="text-lg font-medium mb-4">Profile Information</h3>
                        <form method="POST" action="{{ route('profile.update') }}">
                            @csrf
                            @method('PATCH')

                            <!-- Name -->
                            <div class="mb-4">
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div class="mb-4">
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required autocomplete="username" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Role (Read-only) -->
                            <div class="mb-4">
                                <x-input-label for="role" :value="__('Role')" />
                                <x-text-input id="role" class="block mt-1 w-full bg-gray-100" type="text" name="role" :value="$user->role_name" disabled />
                                <p class="mt-1 text-sm text-gray-500">Role cannot be changed</p>
                            </div>

                            <!-- Department -->
                            <div class="mb-4">
                                <x-input-label for="department" :value="__('Department')" />
                                <x-text-input id="department" class="block mt-1 w-full" type="text" name="department" :value="old('department', $user->department)" autocomplete="organization" />
                                <x-input-error :messages="$errors->get('department')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end">
                                <x-primary-button>
                                    {{ __('Update Profile') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>

                    <!-- Change Password -->
                    <div class="border-t pt-8">
                        <h3 class="text-lg font-medium mb-4">Change Password</h3>
                        <form method="POST" action="{{ route('profile.password') }}">
                            @csrf
                            @method('PATCH')

                            <!-- Current Password -->
                            <div class="mb-4">
                                <x-input-label for="current_password" :value="__('Current Password')" />
                                <x-text-input id="current_password" class="block mt-1 w-full" type="password" name="current_password" required autocomplete="current-password" />
                                <x-input-error :messages="$errors->get('current_password')" class="mt-2" />
                            </div>

                            <!-- New Password -->
                            <div class="mb-4">
                                <x-input-label for="password" :value="__('New Password')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-4">
                                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end">
                                <x-primary-button>
                                    {{ __('Change Password') }}
                                </x-primary-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

