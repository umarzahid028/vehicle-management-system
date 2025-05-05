<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-foreground">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6 py-6 px-4">
            <!-- Stats Grid -->
            <div class="grid gap-6 mb-8 md:grid-cols-2 xl:grid-cols-4">
                <!-- Total Vehicles Card -->
                <div class="p-4 rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-primary/10">
                            <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-muted-foreground">Total Vehicles</p>
                            <p class="text-lg font-semibold text-foreground">{{ $totalVehicles ?? 0 }}</p>
                            <p class="text-sm text-muted-foreground">
                                @if(($vehicleGrowth ?? 0) > 0)
                                    <span class="text-emerald-500">+{{ $vehicleGrowth ?? 0 }}%</span>
                                @else
                                    <span class="text-destructive">{{ $vehicleGrowth ?? 0 }}%</span>
                                @endif
                                from last month
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Active Inspections Card -->
                <div class="p-4 rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-amber-500/10">
                            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-muted-foreground">Active Inspections</p>
                            <p class="text-lg font-semibold text-foreground">{{ $activeInspections ?? 0 }}</p>
                            <p class="text-sm text-muted-foreground">{{ $completedInspections ?? 0 }} completed</p>
                        </div>
                    </div>
                </div>

                <!-- Open Issues Card -->
                <div class="p-4 rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-destructive/10">
                            <svg class="w-6 h-6 text-destructive" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-muted-foreground">Open Issues</p>
                            <p class="text-lg font-semibold text-foreground">{{ $openIssues ?? 0 }}</p>
                            <p class="text-sm text-muted-foreground">{{ $resolvedIssues ?? 0 }} resolved</p>
                        </div>
                    </div>
                </div>

                <!-- Monthly Revenue Card -->
                <div class="p-4 rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-emerald-500/10">
                            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="mb-2 text-sm font-medium text-muted-foreground">Monthly Revenue</p>
                            <p class="text-lg font-semibold text-foreground">${{ number_format($monthlyRevenue ?? 0, 2) }}</p>
                            <p class="text-sm text-muted-foreground">
                                @if(($monthlyRevenue ?? 0) > ($lastMonthRevenue ?? 0))
                                    <span class="text-emerald-500">↑</span>
                                @else
                                    <span class="text-destructive">↓</span>
                                @endif
                                ${{ number_format($lastMonthRevenue ?? 0, 2) }} last month
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Grid -->
            <div class="grid gap-6 mb-8 md:grid-cols-2">
                <!-- Vehicle Status Chart -->
                <div class="p-4 rounded-lg border bg-card text-card-foreground shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-foreground">Vehicle Status Distribution</h3>
                    <div class="h-64" id="vehicleStatusChart"></div>
                </div>

                <!-- Revenue Chart -->
                <div class="p-4 rounded-lg border bg-card text-card-foreground shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-foreground">Revenue Trend</h3>
                    <div class="h-64" id="revenueChart"></div>
                </div>
            </div>

            <!-- Recent Activities -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                <div class="p-4 border-b">
                    <h3 class="text-lg font-semibold text-foreground">Recent Activities</h3>
                </div>
                <div class="p-4">
                    <div class="space-y-4">
                        @forelse($recentActivities ?? [] as $activity)
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary/10">
                                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </span>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-foreground">{{ $activity->description }}</p>
                                    <p class="text-sm text-muted-foreground">{{ $activity->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-muted-foreground">No recent activities</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Vehicle Status Chart
        var vehicleStatusOptions = {
            series: {!! json_encode(array_values($vehicleStatusChart ?? [])) !!},
            chart: {
                type: 'donut',
                height: 250,
                background: 'transparent',
                foreColor: '#a1a1aa' // text color for labels
            },
            labels: {!! json_encode(array_keys($vehicleStatusChart ?? [])) !!},
            colors: ['#0ea5e9', '#10b981', '#f59e0b', '#ef4444'],
            legend: {
                position: 'bottom',
                labels: {
                    colors: '#a1a1aa'
                }
            },
            theme: {
                mode: 'dark'
            }
        };
        new ApexCharts(document.querySelector("#vehicleStatusChart"), vehicleStatusOptions).render();

        // Revenue Chart
        var revenueOptions = {
            series: [{
                name: 'Revenue',
                data: {!! json_encode(array_values($revenueChart ?? [])) !!}
            }],
            chart: {
                type: 'area',
                height: 250,
                toolbar: {
                    show: false
                },
                background: 'transparent',
                foreColor: '#a1a1aa' // text color for labels
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 2,
                colors: ['#0ea5e9']
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.3,
                    opacityTo: 0.1,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: {!! json_encode(array_keys($revenueChart ?? [])) !!},
                labels: {
                    style: {
                        colors: '#a1a1aa'
                    }
                },
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#a1a1aa'
                    }
                }
            },
            grid: {
                borderColor: '#27272a',
                strokeDashArray: 4,
                xaxis: {
                    lines: {
                        show: true
                    }
                },
                yaxis: {
                    lines: {
                        show: true
                    }
                }
            },
            tooltip: {
                y: {
                    formatter: function (val) {
                        return "$" + val.toLocaleString()
                    }
                },
                theme: 'dark'
            },
            theme: {
                mode: 'dark'
            }
        };
        new ApexCharts(document.querySelector("#revenueChart"), revenueOptions).render();
    </script>
    @endpush
</x-app-layout>
