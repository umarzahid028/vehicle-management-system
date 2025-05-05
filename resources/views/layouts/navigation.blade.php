<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('vehicles.index')" :active="request()->routeIs('vehicles.index') || request()->routeIs('vehicles.show') || request()->routeIs('vehicles.edit') || request()->routeIs('vehicles.create')">
                        {{ __('Vehicles') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.index') || request()->routeIs('tasks.create') || request()->routeIs('tasks.edit')">
                        {{ __('Tasks') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('vendors.index')" :active="request()->routeIs('vendors.*') || request()->routeIs('vendor-types.*')">
                        {{ __('Vendors') }}
                    </x-nav-link>
                    
                    <x-nav-link :href="route('sales.goodwill-claims.index')" :active="request()->routeIs('sales.goodwill-claims.*')">
                        {{ __('Goodwill Claims') }}
                    </x-nav-link>
                    
                    @can('approve-estimates')
                    <x-nav-link :href="route('vendor-estimates.pending')" :active="request()->routeIs('vendor-estimates.*')">
                        {{ __('Pending Estimates') }}
                        @php
                            $pendingCount = \App\Models\VendorEstimate::where('status', 'pending')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </x-nav-link>
                    @endcan

                </div>
            </div>

            <!-- Notifications Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-notification-dropdown align="right" width="96">
                    <x-slot name="trigger">
                        <button class="relative inline-flex items-center p-2 text-sm font-medium text-gray-500 rounded-full hover:text-gray-700 focus:outline-none transition duration-150 ease-in-out">
                            <x-heroicon-o-bell class="w-6 h-6" />
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <span class="absolute top-0 right-0 inline-flex items-center justify-center w-4 h-4 text-xs font-bold text-white bg-red-500 rounded-full">
                                    {{ auth()->user()->unreadNotifications->count() }}
                                </span>
                            @endif
                        </button>
                    </x-slot>
                </x-notification-dropdown>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ml-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ml-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
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
            <div class="-mr-2 flex items-center sm:hidden">
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
            
            <div class="space-y-1">
                <x-responsive-nav-link :href="route('vehicles.index')" :active="request()->routeIs('vehicles.index')">
                    {{ __('Vehicles') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('vehicles.intake')" :active="request()->routeIs('vehicles.intake')">
                    {{ __('Intake & Dispatch') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('vehicles.frontline.index')" :active="request()->routeIs('vehicles.frontline.*')">
                    {{ __('Frontline Ready') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('post-sale.index')" :active="request()->routeIs('post-sale.*') || request()->routeIs('goodwill-repairs.*')">
                    {{ __('Post-Sale Management') }}
                </x-responsive-nav-link>
            </div>
            
            <x-responsive-nav-link :href="route('tasks.index')" :active="request()->routeIs('tasks.index') || request()->routeIs('tasks.create') || request()->routeIs('tasks.edit')">
                {{ __('Tasks') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('vendors.index')" :active="request()->routeIs('vendors.*') || request()->routeIs('vendor-types.*')">
                {{ __('Vendors') }}
            </x-responsive-nav-link>
            
            <x-responsive-nav-link :href="route('sales.goodwill-claims.index')" :active="request()->routeIs('sales.goodwill-claims.*')">
                {{ __('Goodwill Claims') }}
            </x-responsive-nav-link>
            
            @can('approve-estimates')
            <x-responsive-nav-link :href="route('vendor-estimates.pending')" :active="request()->routeIs('vendor-estimates.*')">
                {{ __('Pending Estimates') }}
                @php
                    $pendingCount = \App\Models\VendorEstimate::where('status', 'pending')->count();
                @endphp
                @if($pendingCount > 0)
                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        {{ $pendingCount }}
                    </span>
                @endif
            </x-responsive-nav-link>
            @endcan
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
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

    <!-- Settings Section -->
    <div class="px-3 py-2">
        <h3 class="mb-2 px-4 text-xs font-semibold tracking-wider text-muted-foreground uppercase">
            Administration
        </h3>
        <div class="space-y-1">
            <a href="{{ route('admin.settings.index') }}" 
                class="group flex items-center rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.settings.*') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                <x-heroicon-o-cog-6-tooth class="mr-2 h-4 w-4" />
                System Settings
            </a>
            <a href="{{ route('admin.roles.index') }}" 
                class="group flex items-center rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.roles.*') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                <x-heroicon-o-shield-check class="mr-2 h-4 w-4" />
                Roles & Permissions
            </a>
            <a href="{{ route('admin.users.index') }}" 
                class="group flex items-center rounded-md px-3 py-2 text-sm font-medium {{ request()->routeIs('admin.users.*') ? 'bg-accent text-accent-foreground' : 'text-muted-foreground hover:bg-accent hover:text-accent-foreground' }}">
                <x-heroicon-o-users class="mr-2 h-4 w-4" />
                Users
            </a>
        </div>
    </div>
</nav>
