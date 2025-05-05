<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-tight">Sales Dashboard</h2>
                <p class="text-sm text-muted-foreground">Track your sales performance and metrics</p>
            </div>
        </div>
    </x-slot>

    <div class="container mx-auto space-y-6 ">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- Total Sales Card -->
            <x-ui.card>
                <div class="p-6 flex flex-col space-y-2">
                    <div class="flex items-center justify-between space-y-0">
                        <h3 class="tracking-tight text-sm font-medium">Total Sales</h3>
                        <x-heroicon-o-chart-bar class="h-4 w-4 text-muted-foreground"/>
                    </div>
                    <div class="text-2xl font-bold">{{ $performance['total_sales'] }}</div>
                    <div class="text-xs text-muted-foreground">
                        All-time sales count
                    </div>
                </div>
            </x-ui.card>

            <!-- Monthly Sales Card -->
            <x-ui.card>
                <div class="p-6 flex flex-col space-y-2">
                    <div class="flex items-center justify-between space-y-0">
                        <h3 class="tracking-tight text-sm font-medium">Monthly Sales</h3>
                        <x-heroicon-o-calendar class="h-4 w-4 text-muted-foreground"/>
                    </div>
                    <div class="text-2xl font-bold">{{ $performance['monthly_sales'] }}</div>
                    <div class="text-xs text-muted-foreground">
                        Sales this month
                    </div>
                </div>
            </x-ui.card>

            <!-- Total Amount Card -->
            <x-ui.card>
                <div class="p-6 flex flex-col space-y-2">
                    <div class="flex items-center justify-between space-y-0">
                        <h3 class="tracking-tight text-sm font-medium">Total Amount</h3>
                        <x-heroicon-o-currency-dollar class="h-4 w-4 text-muted-foreground"/>
                    </div>
                    <div class="text-2xl font-bold">${{ number_format($performance['total_amount'], 2) }}</div>
                    <div class="text-xs text-muted-foreground">
                        All-time revenue
                    </div>
                </div>
            </x-ui.card>

            <!-- Monthly Amount Card -->
            <x-ui.card>
                <div class="p-6 flex flex-col space-y-2">
                    <div class="flex items-center justify-between space-y-0">
                        <h3 class="tracking-tight text-sm font-medium">Monthly Amount</h3>
                        <x-heroicon-o-banknotes class="h-4 w-4 text-muted-foreground"/>
                    </div>
                    <div class="text-2xl font-bold">${{ number_format($performance['monthly_amount'], 2) }}</div>
                    <div class="text-xs text-muted-foreground">
                        Revenue this month
                    </div>
                </div>
            </x-ui.card>
        </div>

        <!-- Sales Stats Table -->
        <x-ui.card>
            <div class="p-0">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-medium">Sales Statistics</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">Date</th>
                                <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">Sales Count</th>
                                <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">Total Amount</th>
                                <th class="h-12 px-6 text-left align-middle font-medium text-muted-foreground">Average Sale</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($salesStats as $stat)
                            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                <td class="p-4 px-6 align-middle">{{ $stat->date }}</td>
                                <td class="p-4 px-6 align-middle">{{ $stat->count }}</td>
                                <td class="p-4 px-6 align-middle">${{ number_format($stat->total, 2) }}</td>
                                <td class="p-4 px-6 align-middle">
                                    ${{ $stat->count > 0 ? number_format($stat->total / $stat->count, 2) : '0.00' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </x-ui.card>
    </div>
</x-app-layout>
