<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-tight">Transport Dashboard</h2>
                <p class="text-sm text-muted-foreground">Monitor and manage your transport operations.</p>
            </div>
        </div>
    </x-slot>

    <div class="container mx-auto space-y-6 p-4 sm:p-6 lg:p-8">
        <!-- Stats Overview -->
        <div class="grid gap-4 md:grid-cols-3">
            <!-- Pending Transports -->
            <x-ui.card>
                <div class="p-6 flex flex-col space-y-2">
                    <div class="flex items-center justify-between space-y-0">
                        <h3 class="tracking-tight text-sm font-medium">Pending</h3>
                        <x-heroicon-o-clock class="h-4 w-4 text-muted-foreground"/>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-2xl font-bold">{{ $transportStats['pending'] }}</div>
                        <div class="flex items-center text-xs text-muted-foreground">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-warning/10 text-warning">
                                Awaiting pickup
                            </span>
                        </div>
                    </div>
                    <div class="text-xs text-muted-foreground">
                        Transports pending assignment
                    </div>
                </div>
            </x-ui.card>

            <!-- In Transit -->
            <x-ui.card>
                <div class="p-6 flex flex-col space-y-2">
                    <div class="flex items-center justify-between space-y-0">
                        <h3 class="tracking-tight text-sm font-medium">In Transit</h3>
                        <x-heroicon-o-truck class="h-4 w-4 text-muted-foreground"/>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-2xl font-bold">{{ $transportStats['in_transit'] }}</div>
                        <div class="flex items-center text-xs text-muted-foreground">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                Active
                            </span>
                        </div>
                    </div>
                    <div class="text-xs text-muted-foreground">
                        Vehicles currently in transit
                    </div>
                </div>
            </x-ui.card>

            <!-- Delivered -->
            <x-ui.card>
                <div class="p-6 flex flex-col space-y-2">
                    <div class="flex items-center justify-between space-y-0">
                        <h3 class="tracking-tight text-sm font-medium">Delivered</h3>
                        <x-heroicon-o-check-circle class="h-4 w-4 text-muted-foreground"/>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-2xl font-bold">{{ $transportStats['delivered'] }}</div>
                        <div class="flex items-center text-xs text-muted-foreground">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-success/10 text-success">
                                Completed
                            </span>
                        </div>
                    </div>
                    <div class="text-xs text-muted-foreground">
                        Successfully delivered vehicles
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Recent Activity -->
        <x-ui.card>
            <div class="p-6">
                <div class="flex flex-col space-y-2">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold">Recent Activity</h3>
                        <a href="{{ route('transports.index') }}" class="text-sm font-medium text-primary hover:underline">
                            View all
                        </a>
                    </div>
                    <p class="text-sm text-muted-foreground">Latest transport updates and activities</p>
                </div>
                <div class="mt-4">
                    <div class="relative mb-4">
                        <div class="absolute inset-0 flex items-center">
                            <span class="w-full border-t"></span>
                        </div>
                        <div class="relative flex justify-center text-xs uppercase">
                            <span class="bg-background px-2 text-muted-foreground">Last 30 days</span>
                        </div>
                    </div>

                    @if($recentActivities->isEmpty())
                        <div class="text-center py-8">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-gray-100 mb-4">
                                <x-heroicon-o-inbox class="h-6 w-6 text-gray-400"/>
                            </div>
                            <p class="text-muted-foreground">No recent transport activities found</p>
                            <a href="{{ route('transports.index') }}" class="mt-2 inline-flex items-center text-sm font-medium text-primary hover:underline">
                                <x-heroicon-o-plus class="mr-1 h-4 w-4" />
                                View all transports
                            </a>
                        </div>
                    @else
                        <div class="space-y-6">
                            @foreach($recentActivities as $activity)
                                <div class="flex items-start space-x-4">
                                    <div class="flex items-center justify-center w-10 h-10 rounded-full bg-primary/10">
                                        @if($activity->status === 'pending')
                                            <x-heroicon-o-clock class="h-5 w-5 text-warning"/>
                                        @elseif($activity->status === 'in_transit')
                                            <x-heroicon-o-truck class="h-5 w-5 text-primary"/>
                                        @elseif($activity->status === 'delivered')
                                            <x-heroicon-o-check-circle class="h-5 w-5 text-success"/>
                                        @else
                                            <x-heroicon-o-truck class="h-5 w-5 text-muted-foreground"/>
                                        @endif
                                    </div>
                                    <div class="flex-1 space-y-1">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center">
                                                <h4 class="text-sm font-medium">
                                                    {{ $activity->vehicle->make }} {{ $activity->vehicle->model }}
                                                </h4>
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                    @if($activity->status === 'pending') bg-warning/10 text-warning
                                                    @elseif($activity->status === 'in_transit') bg-primary/10 text-primary
                                                    @elseif($activity->status === 'delivered') bg-success/10 text-success
                                                    @else bg-gray-100 text-gray-800 @endif">
                                                    {{ ucfirst(str_replace('_', ' ', $activity->status)) }}
                                                </span>
                                            </div>
                                            <time class="text-xs text-muted-foreground">{{ $activity->updated_at->diffForHumans() }}</time>
                                        </div>
                                        <p class="text-sm text-muted-foreground">
                                            {{ $activity->origin }} → {{ $activity->destination }}
                                        </p>
                                        <div class="flex gap-4 mt-1 text-xs">
                                            <span class="text-muted-foreground">Stock #: {{ $activity->vehicle->stock_number }}</span>
                                            @if($activity->pickup_date)
                                                <span class="text-muted-foreground">Pickup: {{ $activity->pickup_date->format('M d') }}</span>
                                            @endif
                                            @if($activity->delivery_date)
                                                <span class="text-muted-foreground">Delivery: {{ $activity->delivery_date->format('M d') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <a href="{{ route('transports.show', ['transport' => $activity->id]) }}" class="text-sm font-medium text-primary hover:underline whitespace-nowrap">
                                        View
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </x-ui.card>

        <!-- Quick Filters -->
        <x-ui.card>
            <div class="p-6">
                <div class="flex flex-col space-y-2">
                    <h3 class="text-lg font-semibold">Quick Access</h3>
                    <p class="text-sm text-muted-foreground">Filter your transport records</p>
                </div>
                <div class="mt-4 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <a href="{{ route('transports.index', ['status' => 'pending']) }}" 
                        class="inline-flex items-center justify-center p-4 rounded-lg border border-muted bg-muted/10 hover:bg-muted/20 transition-colors">
                        <div class="flex flex-col items-center text-center space-y-2">
                            <x-heroicon-o-clock class="h-6 w-6 text-warning"/>
                            <span class="text-sm font-medium">Pending Transports</span>
                        </div>
                    </a>
                    <a href="{{ route('transports.index', ['status' => 'in_transit']) }}" 
                        class="inline-flex items-center justify-center p-4 rounded-lg border border-muted bg-muted/10 hover:bg-muted/20 transition-colors">
                        <div class="flex flex-col items-center text-center space-y-2">
                            <x-heroicon-o-truck class="h-6 w-6 text-primary"/>
                            <span class="text-sm font-medium">In Transit</span>
                        </div>
                    </a>
                    <a href="{{ route('transports.index', ['status' => 'delivered']) }}" 
                        class="inline-flex items-center justify-center p-4 rounded-lg border border-muted bg-muted/10 hover:bg-muted/20 transition-colors">
                        <div class="flex flex-col items-center text-center space-y-2">
                            <x-heroicon-o-check-circle class="h-6 w-6 text-success"/>
                            <span class="text-sm font-medium">Completed Transports</span>
                        </div>
                    </a>
                    <a href="{{ route('transports.index', ['acknowledged' => 'false']) }}" 
                        class="inline-flex items-center justify-center p-4 rounded-lg border border-muted bg-muted/10 hover:bg-muted/20 transition-colors">
                        <div class="flex flex-col items-center text-center space-y-2">
                            <x-heroicon-o-bell-alert class="h-6 w-6 text-destructive"/>
                            <span class="text-sm font-medium">Unacknowledged</span>
                        </div>
                    </a>
                </div>
            </div>
        </x-ui.card>
    </div>
</x-app-layout> 