<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-tight">Manager Dashboard</h2>
                <p class="text-sm text-muted-foreground">View and manage your dealership's performance metrics.</p>
            </div>
        </div>
    </x-slot>

    <div class="container mx-auto space-y-6 p-4 sm:p-6 lg:p-8">
        <!-- Stats Overview -->
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
            <!-- Vehicle Stats -->
            <x-ui.card>
                <div class="p-6 flex flex-col space-y-2">
                    <div class="flex items-center justify-between space-y-0">
                        <h3 class="tracking-tight text-sm font-medium">Total Vehicles</h3>
                        <x-heroicon-o-truck class="h-4 w-4 text-muted-foreground"/>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-2xl font-bold">{{ $vehicleStats['total'] }}</div>
                        <div class="flex items-center text-xs text-muted-foreground">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                +{{ $vehicleStats['in_stock'] }} in stock
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-muted-foreground">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-wrench-screwdriver class="h-3 w-3"/>
                            {{ $vehicleStats['in_recon'] }} in recon
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-check-circle class="h-3 w-3"/>
                            {{ $vehicleStats['sold'] }} sold
                        </span>
                    </div>
                </div>
            </x-ui.card>

            <!-- Transport Stats -->
            <x-ui.card>
                <div class="p-6 flex flex-col space-y-2">
                    <div class="flex items-center justify-between space-y-0">
                        <h3 class="tracking-tight text-sm font-medium">Transports</h3>
                        <x-heroicon-o-truck class="h-4 w-4 text-muted-foreground"/>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-2xl font-bold">{{ $transportStats['pending'] + $transportStats['in_transit'] + $transportStats['delivered'] }}</div>
                        <div class="flex items-center text-xs text-muted-foreground">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-warning/10 text-warning">
                                {{ $transportStats['pending'] }} pending
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-muted-foreground">
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-arrow-path class="h-3 w-3"/>
                            {{ $transportStats['in_transit'] }} in transit
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <x-heroicon-o-check-circle class="h-3 w-3"/>
                            {{ $transportStats['delivered'] }} delivered
                        </span>
                    </div>
                </div>
            </x-ui.card>

            <!-- Sales Overview -->
            <x-ui.card>
                <div class="p-6 flex flex-col space-y-2">
                    <div class="flex items-center justify-between space-y-0">
                        <h3 class="tracking-tight text-sm font-medium">Monthly Sales</h3>
                        <x-heroicon-o-currency-dollar class="h-4 w-4 text-muted-foreground"/>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-2xl font-bold">${{ number_format($salesData->sum('total'), 2) }}</div>
                        <div class="flex items-center text-xs text-muted-foreground">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-success/10 text-success">
                                {{ $salesData->sum('count') }} sales
                            </span>
                        </div>
                    </div>
                    <div class="text-xs text-muted-foreground">
                        Monthly revenue from vehicle sales
                    </div>
                </div>
            </x-ui.card>

            <!-- Estimates Overview -->
            <x-ui.card>
                <div class="p-6 flex flex-col space-y-2">
                    <div class="flex items-center justify-between space-y-0">
                        <h3 class="tracking-tight text-sm font-medium">Monthly Estimates</h3>
                        <x-heroicon-o-clipboard-document-check class="h-4 w-4 text-muted-foreground"/>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="text-2xl font-bold">${{ number_format($estimatesData->sum('total'), 2) }}</div>
                        <div class="flex items-center text-xs text-muted-foreground">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-info/10 text-info">
                                {{ $estimatesData->sum('count') }} estimates
                            </span>
                        </div>
                    </div>
                    <div class="text-xs text-muted-foreground">
                        Total value of repair estimates
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- NEW COMPONENTS START HERE -->
        
        <!-- Row 2: Inspection Status and Repair Status Overview -->
        <div class="grid gap-4 md:grid-cols-2">
            <!-- Recent Inspections -->
            <x-ui.card>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium">Recent Inspections</h3>
                        <x-heroicon-o-clipboard-document-check class="h-5 w-5 text-muted-foreground"/>
                    </div>
                    
                    @if(isset($recentInspections) && count($recentInspections) > 0)
                        <div class="space-y-4">
                            @foreach($recentInspections as $inspection)
                                <div class="flex items-center justify-between p-3 bg-muted/20 rounded-md">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $inspection->vehicle->year }} {{ $inspection->vehicle->make }} {{ $inspection->vehicle->model }}</span>
                                        <span class="text-xs text-muted-foreground">Stock #{{ $inspection->vehicle->stock_number }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm">
                                            @if($inspection->status === 'completed')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-success/10 text-success">Completed</span>
                                            @elseif($inspection->status === 'in_progress')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-info/10 text-info">In Progress</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-warning/10 text-warning">Pending</span>
                                            @endif
                                        </span>
                                        <a href="{{ route('inspection.inspections.show', $inspection) }}" class="text-xs text-primary hover:underline">
                                            View
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-4 text-center">
                            <a href="{{ route('inspection.inspections.index') }}" class="text-sm text-primary hover:underline">View all inspections</a>
                        </div>
                    @else
                        <div class="text-sm text-muted-foreground text-center py-6">
                            No recent inspections found
                        </div>
                    @endif
                </div>
            </x-ui.card>

            <!-- Repair Status Overview -->
            <x-ui.card>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium">Repair Status Overview</h3>
                        <x-heroicon-o-wrench-screwdriver class="h-5 w-5 text-muted-foreground"/>
                    </div>
                    
                    @if(isset($repairStats))
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Vehicles Needing Repair -->
                            <div class="bg-muted/20 rounded-md p-4">
                                <div class="text-3xl font-bold text-primary mb-1">{{ $repairStats['needs_repair'] ?? 0 }}</div>
                                <div class="text-sm text-muted-foreground">Vehicles needing repair</div>
                            </div>
                            
                            <!-- Repairs Assigned -->
                            <div class="bg-muted/20 rounded-md p-4">
                                <div class="text-3xl font-bold text-warning mb-1">{{ $repairStats['repair_assigned'] ?? 0 }}</div>
                                <div class="text-sm text-muted-foreground">Repairs assigned</div>
                            </div>
                            
                            <!-- Avg Repair Time -->
                            <div class="bg-muted/20 rounded-md p-4">
                                <div class="text-3xl font-bold text-info mb-1">{{ $repairStats['avg_repair_time'] ?? 0 }}</div>
                                <div class="text-sm text-muted-foreground">Avg repair time (days)</div>
                            </div>
                            
                            <!-- Total Repair Cost -->
                            <div class="bg-muted/20 rounded-md p-4">
                                <div class="text-3xl font-bold text-success mb-1">${{ number_format($repairStats['total_cost'] ?? 0, 0) }}</div>
                                <div class="text-sm text-muted-foreground">Total repair costs</div>
                            </div>
                        </div>
                    @else
                        <div class="text-sm text-muted-foreground text-center py-6">
                            No repair statistics available
                        </div>
                    @endif
                </div>
            </x-ui.card>
        </div>

        <!-- Row 3: Sales Performance and Inventory Aging -->
        <div class="grid gap-4 md:grid-cols-2">
            <!-- Sales Performance -->
            <x-ui.card>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium">Sales Team Performance</h3>
                        <x-heroicon-o-user-group class="h-5 w-5 text-muted-foreground"/>
                    </div>
                    
                    @if(isset($salesPerformance) && count($salesPerformance) > 0)
                        <div class="space-y-3">
                            @foreach($salesPerformance as $performer)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-shrink-0 h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center">
                                            <span class="text-xs font-medium text-primary">{{ strtoupper(substr($performer['name'], 0, 2)) }}</span>
                                        </div>
                                        <span class="font-medium">{{ $performer['name'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <div class="text-center">
                                            <div class="text-lg font-bold">{{ $performer['sales_count'] }}</div>
                                            <div class="text-xs text-muted-foreground">Sales</div>
                                        </div>
                                        <div class="text-center">
                                            <div class="text-lg font-bold">${{ number_format($performer['sales_amount'], 0) }}</div>
                                            <div class="text-xs text-muted-foreground">Revenue</div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-sm text-muted-foreground text-center py-6">
                            No sales performance data available
                        </div>
                    @endif
                </div>
            </x-ui.card>

            <!-- Inventory Aging -->
            <x-ui.card>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium">Inventory Aging</h3>
                        <x-heroicon-o-clock class="h-5 w-5 text-muted-foreground"/>
                    </div>
                    
                    @if(isset($inventoryAging))
                        <div class="grid grid-cols-2 gap-4">
                            <!-- 0-30 Days -->
                            <div class="bg-muted/20 rounded-md p-4">
                                <div class="text-3xl font-bold text-success mb-1">{{ $inventoryAging['0_30'] ?? 0 }}</div>
                                <div class="text-sm text-muted-foreground">0-30 days</div>
                            </div>
                            
                            <!-- 31-60 Days -->
                            <div class="bg-muted/20 rounded-md p-4">
                                <div class="text-3xl font-bold text-info mb-1">{{ $inventoryAging['31_60'] ?? 0 }}</div>
                                <div class="text-sm text-muted-foreground">31-60 days</div>
                            </div>
                            
                            <!-- 61-90 Days -->
                            <div class="bg-muted/20 rounded-md p-4">
                                <div class="text-3xl font-bold text-warning mb-1">{{ $inventoryAging['61_90'] ?? 0 }}</div>
                                <div class="text-sm text-muted-foreground">61-90 days</div>
                            </div>
                            
                            <!-- 90+ Days -->
                            <div class="bg-muted/20 rounded-md p-4">
                                <div class="text-3xl font-bold text-destructive mb-1">{{ $inventoryAging['90_plus'] ?? 0 }}</div>
                                <div class="text-sm text-muted-foreground">90+ days</div>
                            </div>
                        </div>
                        
                        <div class="mt-4 text-sm text-muted-foreground">
                            <div class="flex justify-between">
                                <span>Average days in inventory:</span>
                                <span class="font-medium">{{ $inventoryAging['avg_days'] ?? 0 }} days</span>
                            </div>
                        </div>
                    @else
                        <div class="text-sm text-muted-foreground text-center py-6">
                            No inventory aging data available
                        </div>
                    @endif
                </div>
            </x-ui.card>
        </div>

        <!-- Row 4: Vendor Performance -->
        <div class="grid gap-4">
            <!-- Vendor Performance -->
            <x-ui.card>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium">Vendor Performance</h3>
                        <x-heroicon-o-building-storefront class="h-5 w-5 text-muted-foreground"/>
                    </div>
                    
                    @if(isset($vendorPerformance) && count($vendorPerformance) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-2 px-3 font-medium">Vendor</th>
                                        <th class="text-center py-2 px-3 font-medium">Total Jobs</th>
                                        <th class="text-center py-2 px-3 font-medium">Completed</th>
                                        <th class="text-center py-2 px-3 font-medium">Avg Completion</th>
                                        <th class="text-right py-2 px-3 font-medium">Total Cost</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y">
                                    @foreach($vendorPerformance as $vendor)
                                        <tr>
                                            <td class="py-2 px-3">{{ $vendor['name'] }}</td>
                                            <td class="py-2 px-3 text-center">{{ $vendor['total_jobs'] }}</td>
                                            <td class="py-2 px-3 text-center">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $vendor['completion_rate'] >= 80 ? 'bg-success/10 text-success' : ($vendor['completion_rate'] >= 60 ? 'bg-warning/10 text-warning' : 'bg-destructive/10 text-destructive') }}">
                                                    {{ $vendor['completion_rate'] }}%
                                                </span>
                                            </td>
                                            <td class="py-2 px-3 text-center">{{ $vendor['avg_days'] }} days</td>
                                            <td class="py-2 px-3 text-right">${{ number_format($vendor['total_cost'], 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-sm text-muted-foreground text-center py-6">
                            No vendor performance data available
                        </div>
                    @endif
                </div>
            </x-ui.card>
        </div>
    </div>
</x-app-layout> 