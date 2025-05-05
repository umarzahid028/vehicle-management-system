<div x-data="{ open: true }" class="min-h-screen h-full flex flex-col flex-shrink-0 w-64 bg-white border-r border-gray-200">
    <!-- Sidebar header -->
    <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200">
        <a href="" class="flex items-center">
            <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
            <span class="ml-2 text-lg font-medium">TrevinosAuto</span>
        </a>
        <button @click="open = !open" class="lg:hidden text-gray-500 hover:text-gray-600">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <!-- Sidebar content -->
    <div class="flex-1 overflow-y-auto p-4">
        <nav class="space-y-1">
            @role('Vendor')
            <!-- Vendor Dashboard -->
            <a href="{{ route('vendor.dashboard') }}" class="{{ request()->routeIs('vendor.dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                <svg class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Dashboard
            </a>

            <!-- Assigned Inspections -->
            <a href="{{ route('vendor.inspections.index') }}" class="{{ request()->routeIs('vendor.inspections.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                <x-heroicon-o-wrench-screwdriver class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" />
                <span class="flex-1">Assigned Inspections</span>
                @if(auth()->user()->unreadNotifications->where('type', 'App\Notifications\InspectionAssigned')->count() > 0)
                    <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        {{ auth()->user()->unreadNotifications->where('type', 'App\Notifications\InspectionAssigned')->count() }}
                    </span>
                @endif
            </a>

            <!-- Inspection History -->
            <a href="{{ route('vendor.inspection-history') }}" class="{{ request()->routeIs('vendor.inspection-history') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                <x-heroicon-o-clipboard-document-check class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" />
                <span class="flex-1">Inspection History</span>
                @if(auth()->user()->unreadNotifications->where('type', 'App\Notifications\InspectionCompleted')->count() > 0)
                    <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                        {{ auth()->user()->unreadNotifications->where('type', 'App\Notifications\InspectionCompleted')->count() }}
                    </span>
                @endif
            </a>
            @endrole

            <!-- Regular Dashboard -->
            @if(!auth()->user()->hasRole('Vendor'))
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                    <svg class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    Dashboard
                </a>

                @role('Transporter')
                <!-- Transporter Section -->
                <div class="pt-2">
                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Transport Management
                    </p>
                    
                    <!-- Assigned Transports -->
                    <a href="{{ route('transports.index') }}" class="{{ request()->routeIs('transports.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                        <span class="flex-1">My Transports</span>
                        @if(isset($transporterCount) && $transporterCount > 0)
                            <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                {{ $transporterCount }}
                            </span>
                        @endif
                    </a>
                </div>
                @endrole

                @if(!auth()->user()->hasRole('Vendor') && !auth()->user()->hasRole('Transporter'))
                    @hasanyrole('Admin|Sales Manager')
                    <!-- Vehicle Management Section -->
                    <div class="pt-2">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Vehicle Management
                        </p>
                        <a href="{{ route('vehicles.index') }}" class="{{ request()->routeIs('vehicles.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <svg class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="flex-1">Vehicle Management</span>
                            @if(isset($newVehicleCount) && $newVehicleCount > 0)
                                <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 sidebar-vehicle-badge">
                                    {{ $newVehicleCount }}
                                </span>
                            @else
                                <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 sidebar-vehicle-badge hidden">
                                    0
                                </span>
                            @endif
                        </a>
                        <!-- Transport Management -->
                        <a href="{{ route('transports.index') }}" class="{{ request()->routeIs('transports.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                            <span class="flex-1">Transport Management</span>
                            @if(isset($transportCount) && $transportCount > 0)
                                <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                    {{ $transportCount }}
                                </span>
                            @endif
                        </a>

                        <!-- Transporter Management -->
                        <a href="{{ route('transporters.index') }}" class="{{ request()->routeIs('transporters.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Transporter Management
                        </a>

                        <!-- Vehicle Inspections -->
                        <a href="{{ route('inspection.inspections.index') }}" class="{{ request()->routeIs('inspection.inspections.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 mt-1 text-sm font-medium rounded-md">
                            <x-heroicon-o-clipboard-document-check class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" />
                            <span class="flex-1">Vehicle Inspections</span>
                            @php
                                $incompleteInspectionsCount = \App\Models\VehicleInspection::where('status', '!=', 'completed')->count();
                            @endphp
                            @if($incompleteInspectionsCount > 0)
                                <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $incompleteInspectionsCount }}
                                </span>
                            @endif
                            @if(auth()->user()->unreadNotifications->where('type', 'App\Notifications\InspectionSubmitted')->count() > 0)
                                <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    {{ auth()->user()->unreadNotifications->where('type', 'App\Notifications\InspectionSubmitted')->count() }}
                                </span>
                            @endif
                        </a>

                        <!-- Offsite Vendor Inspections (for Recon Manager only) -->
                      
                    </div>

                    <!-- Inspection Configuration -->
                    <div class="pt-2">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Inspection Setup
                        </p>
                        
                        <!-- Inspection Stages -->
                        <div x-data="{ openStages: {{ request()->routeIs('inspection.stages.*') || request()->routeIs('inspection.items.*') ? 'true' : 'false' }} }">
                            <button @click="openStages = !openStages" class="w-full group flex items-center px-3 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                                <x-heroicon-o-adjustments-horizontal class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" />
                                <span class="flex-1">Configure Stages</span>
                                <svg class="text-gray-300 ml-3 h-5 w-5 transform transition-transform" :class="{ 'rotate-90': openStages }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                            
                            <div x-show="openStages" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="pl-4 pr-2 space-y-1">
                                <a href="{{ route('inspection.stages.index') }}" class="{{ request()->routeIs('inspection.stages.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                    <x-heroicon-o-list-bullet class="text-gray-400 mr-3 flex-shrink-0 h-5 w-5" />
                                    Manage Stages
                                </a>
                                <a href="{{ route('inspection.items.index') }}" class="{{ request()->routeIs('inspection.items.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                                    <x-heroicon-o-clipboard-document-list class="text-gray-400 mr-3 flex-shrink-0 h-5 w-5" />
                                    Inspection Items
                                </a>
                            </div>
                        </div>
                        
                        <!-- Vendors Management -->
                        <a href="{{ route('vendors.index') }}" class="{{ request()->routeIs('vendors.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                            <x-heroicon-o-building-storefront class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" />
                            Vendors
                        </a>
                    </div>
                    @endhasanyrole
                @endif

                @hasrole('Recon Manager')
                        <a href="{{ route('recon.offsite-inspections.index') }}" class="{{ request()->routeIs('recon.offsite-inspections.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 mt-1 text-sm font-medium rounded-md">
                            <x-heroicon-o-building-storefront class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" />
                            <span class="flex-1">Off-Site Vendor </span>
                            @php
                                $offsiteInspectionsCount = \App\Models\InspectionItemResult::whereHas('assignedVendor', function($query) {
                                    $query->whereHas('type', function($q) {
                                        $q->where('is_on_site', false);
                                    });
                                })
                                ->where('repair_completed', false)
                                ->where('status', '!=', 'cancelled')
                                ->count();
                            @endphp
                            @if($offsiteInspectionsCount > 0)
                                <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                    {{ $offsiteInspectionsCount }}
                                </span>
                            @endif
                        </a>
                        @endhasrole

                <!-- Sales Management Section -->
           
                @hasanyrole('Recon Manager|Sales Manager|Admin')
                <div class="pt-5">
                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Sales Management
                    </p>

                    <!-- Sales Team -->
                    
                    <a href="{{ route('sales-team.index') }}" class="{{ request()->routeIs('sales-team.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 mt-1 text-sm font-medium rounded-md">
                        <x-heroicon-o-users class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" />
                        <span class="flex-1">Sales Team</span>
                    </a>

                    <!-- Sales Assignments -->

                    <a href="{{ route('sales.issues.index') }}" class="{{ request()->routeIs('sales.issues.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 mt-1 text-sm font-medium rounded-md">
                        <x-heroicon-o-exclamation-triangle class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" />
                        <span class="flex-1">Sales Issues</span>
                        @if(auth()->user()->unreadNotifications->where('type', 'App\Notifications\SalesIssueCreated')->count() > 0)
                            <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                {{ auth()->user()->unreadNotifications->where('type', 'App\Notifications\SalesIssueCreated')->count() }}
                            </span>
                        @endif
                    </a>
                    
                
                    <a href="{{ route('sales.goodwill-claims.index') }}" class="{{ request()->routeIs('sales.goodwill-claims.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <x-heroicon-o-document-text class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" />
                        <span class="flex-1">Goodwill Claims</span>
                        @if(auth()->user()->unreadNotifications->where('type', 'App\Notifications\GoodwillClaimSubmitted')->count() > 0)
                            <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                {{ auth()->user()->unreadNotifications->where('type', 'App\Notifications\GoodwillClaimSubmitted')->count() }}
                            </span>
                        @endif
                    </a>
                    
                </div>
                @endcanany

                @if(auth()->user()->hasRole('Sales Team'))
                
                <a href="{{ route('sales-team.vehicles.index') }}" class="{{ request()->routeIs('sales-team.vehicles.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 mt-1 text-sm font-medium rounded-md">
                        <x-heroicon-o-truck class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" />
                        <span class="flex-1">Assigned Vehicles</span>
                    </a>
                    <a href="{{ route('sales-team.sales.create') }}" class="{{ request()->routeIs('sales-team.sales.create') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 mt-1 text-sm font-medium rounded-md">
                        <x-heroicon-o-currency-dollar class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" />
                        <span class="flex-1">Create New Sale</span>
                    </a>
                @endif

                <!-- Administration Section -->
                @canany(['view users', 'assign roles'])
                <div class="pt-5">
                    <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        Administration
                    </p>

                    <!-- User Management -->
                    @can('view users')
                    <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 mt-1 text-sm font-medium rounded-md">
                        <x-heroicon-o-users class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" />
                        Users
                    </a>
                    @endcan

                    <!-- Roles Management -->
                    @role('Admin')
                    <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <x-heroicon-o-shield-check class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" />
                        Roles & Permissions
                    </a>
                    @endrole

                    <!-- System Settings -->
                    @can('edit users')
                    <a href="{{ route('admin.settings.index') }}" class="{{ request()->routeIs('admin.settings.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' }} group flex items-center px-3 py-2 text-sm font-medium rounded-md">
                        <x-heroicon-o-cog-6-tooth class="text-gray-500 mr-3 flex-shrink-0 h-6 w-6" />
                        System Settings
                    </a>
                    @endcan
                </div>
                @endcanany
            @endif
        </nav>
    </div>

    <!-- Sidebar footer -->
    <div class="border-t border-gray-200 p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 rounded-full bg-gray-100 p-2 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name ?? 'User' }}</p>
                <div class="flex mt-1 items-center">
                    <a href="{{ route('profile.edit') }}" class="text-xs font-medium text-gray-500 hover:text-gray-700">Profile</a>
                    <span class="mx-1 text-gray-500">|</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-xs font-medium text-gray-500 hover:text-gray-700">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> 