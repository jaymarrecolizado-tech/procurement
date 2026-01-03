<nav x-data="{ open: false }" class="bg-white border-b border-gray-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="text-xl font-bold text-gray-800">
                        DICT Procurement
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    @auth
                        @if(Auth::user()->hasAnyRole(['END_USER', 'PROCUREMENT_OFFICER', 'ADMIN']))
                            <x-nav-link :href="route('purchase-requests.index')" :active="request()->routeIs('purchase-requests.*')">
                                {{ __('Purchase Requests') }}
                            </x-nav-link>
                        @endif
                        
                        @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']))
                            <x-nav-link :href="route('rfqs.index')" :active="request()->routeIs('rfqs.*')">
                                {{ __('RFQ & Canvass') }}
                            </x-nav-link>
                        @endif
                        
                        @if(Auth::user()->hasAnyRole(['BAC_SECRETARIAT', 'BAC_CHAIR', 'BAC_MEMBER', 'ADMIN']))
                            <x-nav-link :href="route('bac-documents.index')" :active="request()->routeIs('bac-documents.*')">
                                {{ __('BAC Documents') }}
                            </x-nav-link>
                        @endif
                        
                        @if(Auth::user()->hasAnyRole(['BAC_MEMBER', 'BAC_CHAIR', 'PROCUREMENT_OFFICER', 'ADMIN']))
                            <x-nav-link :href="route('approvals.dashboard')" :active="request()->routeIs('approvals.*')">
                                {{ __('Approval Dashboard') }}
                            </x-nav-link>
                        @endif
                        
                        @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'BAC_CHAIR', 'ADMIN']))
                            <x-nav-link :href="route('purchase-orders.index')" :active="request()->routeIs('purchase-orders.*')">
                                {{ __('Purchase Orders') }}
                            </x-nav-link>
                        @endif
                        
                        @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'BAC_CHAIR', 'ADMIN']))
                            <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')">
                                {{ __('Reports') }}
                            </x-nav-link>
                        @endif
                        
                        @if(Auth::user()->hasAnyRole(['CANVASSER', 'PROCUREMENT_OFFICER', 'ADMIN']))
                            <x-nav-link :href="route('supplier-repository.index')" :active="request()->routeIs('supplier-repository.*')">
                                {{ __('Supplier Repository') }}
                            </x-nav-link>
                        @endif
                        
                        @if(Auth::user()->hasAnyRole(['CANVASSER']))
                            <x-nav-link :href="route('canvasses.index')" :active="request()->routeIs('canvasses.*')">
                                {{ __('My Canvass Tasks') }}
                            </x-nav-link>
                        @endif
                        
                        @if(Auth::user()->hasRole('ADMIN'))
                            <x-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                                {{ __('User Management') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Notifications -->
            <div class="hidden sm:flex sm:items-center sm:ms-4">
                <x-dropdown align="right" width="80">
                    <x-slot name="trigger">
                        <button class="relative inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            @if(Auth::user()->unreadNotifications->count() > 0)
                                <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-400 ring-2 ring-white"></span>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 border-b border-gray-200">
                            <div class="flex justify-between items-center">
                                <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                                @if(Auth::user()->unreadNotifications->count() > 0)
                                    <form method="POST" action="{{ route('notifications.read-all') }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-xs text-indigo-600 hover:text-indigo-900">Mark all as read</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            @forelse(Auth::user()->notifications->take(10) as $notification)
                                <a href="{{ $notification->data['url'] ?? '#' }}" class="block px-4 py-3 hover:bg-gray-50 border-b border-gray-100 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50' }}">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <p class="text-sm {{ $notification->read_at ? 'text-gray-600' : 'text-gray-900 font-medium' }}">
                                                {{ $notification->data['message'] ?? 'New notification' }}
                                            </p>
                                            <p class="text-xs text-gray-500 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                        @if(!$notification->read_at)
                                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="ml-2" onclick="event.stopPropagation();">
                                                @csrf
                                                <button type="submit" class="text-xs text-gray-400 hover:text-gray-600">×</button>
                                            </form>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <div class="px-4 py-8 text-center text-sm text-gray-500">
                                    No notifications
                                </div>
                            @endforelse
                        </div>
                        @if(Auth::user()->notifications->count() > 10)
                            <div class="px-4 py-2 border-t border-gray-200 text-center">
                                <a href="#" class="text-xs text-indigo-600 hover:text-indigo-900">View all notifications</a>
                            </div>
                        @endif
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-2 text-xs text-gray-500">
                            {{ Auth::user()->role_name }} - {{ Auth::user()->department ?? 'N/A' }}
                        </div>
                        
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            
            @auth
                @if(Auth::user()->hasAnyRole(['END_USER', 'PROCUREMENT_OFFICER', 'ADMIN']))
                    <x-responsive-nav-link :href="route('purchase-requests.index')" :active="request()->routeIs('purchase-requests.*')">
                        {{ __('Purchase Requests') }}
                    </x-responsive-nav-link>
                @endif
                
                @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'ADMIN']))
                    <x-responsive-nav-link :href="route('rfqs.index')" :active="request()->routeIs('rfqs.*')">
                        {{ __('RFQ & Canvass') }}
                    </x-responsive-nav-link>
                @endif
                
                @if(Auth::user()->hasAnyRole(['BAC_SECRETARIAT', 'BAC_CHAIR', 'BAC_MEMBER', 'ADMIN']))
                    <x-responsive-nav-link :href="route('bac-documents.index')" :active="request()->routeIs('bac-documents.*')">
                        {{ __('BAC Documents') }}
                    </x-responsive-nav-link>
                @endif
                
                @if(Auth::user()->hasAnyRole(['BAC_MEMBER', 'BAC_CHAIR', 'PROCUREMENT_OFFICER', 'ADMIN']))
                    <x-responsive-nav-link :href="route('approvals.dashboard')" :active="request()->routeIs('approvals.*')">
                        {{ __('Approval Dashboard') }}
                    </x-responsive-nav-link>
                @endif
                
                @if(Auth::user()->hasAnyRole(['PROCUREMENT_OFFICER', 'BAC_CHAIR', 'ADMIN']))
                    <x-responsive-nav-link :href="route('purchase-orders.index')" :active="request()->routeIs('purchase-orders.*')">
                        {{ __('Purchase Orders') }}
                    </x-responsive-nav-link>
                @endif
                
                @if(Auth::user()->hasAnyRole(['CANVASSER']))
                    <x-responsive-nav-link :href="route('canvasses.index')" :active="request()->routeIs('canvasses.*')">
                        {{ __('My Canvass Tasks') }}
                    </x-responsive-nav-link>
                @endif
                
                @if(Auth::user()->hasRole('ADMIN'))
                    <x-responsive-nav-link :href="route('users.index')" :active="request()->routeIs('users.*')">
                        {{ __('User Management') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                <div class="text-xs text-gray-500">{{ Auth::user()->role_name }} - {{ Auth::user()->department ?? 'N/A' }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>