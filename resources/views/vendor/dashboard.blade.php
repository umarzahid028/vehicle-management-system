<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-zinc-800 dark:text-zinc-200">
                {{ __('Vendor Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <!-- Statistics Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <!-- Total Assigned Card -->
                <div class="bg-white dark:bg-zinc-950 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-800">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-zinc-950 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Total Assigned</h3>
                                <p class="text-3xl font-semibold text-zinc-700 dark:text-zinc-300">{{ $stats['total_assigned'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Inspections Card -->
                <div class="bg-white dark:bg-zinc-950 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-800">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-zinc-950 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Pending</h3>
                                <p class="text-3xl font-semibold text-zinc-700 dark:text-zinc-300">{{ $stats['pending_count'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Urgent Attention Card -->
                <div class="bg-white dark:bg-zinc-950 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-800">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-red-600 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Needs Attention</h3>
                                <p class="text-3xl font-semibold text-zinc-700 dark:text-zinc-300">{{ $stats['urgent_count'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Completed This Week Card -->
                <div class="bg-white dark:bg-zinc-950 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-800">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 bg-zinc-950 rounded-md p-3">
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Completed This Week</h3>
                                <p class="text-3xl font-semibold text-zinc-700 dark:text-zinc-300">{{ $stats['this_week_completed'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Pending Inspections -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-zinc-950 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-800">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">Pending Inspections</h3>
                            
                            @if($pendingInspections->isEmpty())
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-zinc-900 dark:text-zinc-100">No pending inspections</h3>
                                    <p class="mt-1 text-sm text-zinc-500">You're all caught up!</p>
                                </div>
                            @else
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                                        <thead class="bg-zinc-50 dark:bg-zinc-900">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Vehicle</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Items</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Status</th>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Waiting Time</th>
                                                <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white dark:bg-zinc-950 divide-y divide-zinc-200 dark:divide-zinc-800">
                                            @foreach($pendingInspections as $inspection)
                                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900">
                                                    <td class="px-6 py-4">
                                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                            {{ $inspection->vehicle->year }} {{ $inspection->vehicle->make }} {{ $inspection->vehicle->model }}
                                                        </div>
                                                        <div class="text-sm text-zinc-500">
                                                            VIN: {{ $inspection->vehicle->vin }}
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                                            @php
                                                                $items = $inspection->itemResults->where('vendor_id', auth()->user()->vendor->id);
                                                                $totalItems = $items->count();
                                                                $pendingItems = $items->whereIn('status', ['pending', 'fail', 'warning'])->count();
                                                                $diagnosticItems = $items->whereNotNull('diagnostic_status')->count();
                                                                $repairItems = $items->where('requires_repair', true)->where('repair_completed', false)->count();
                                                            @endphp
                                                            {{ $totalItems }} items
                                                            @if($pendingItems > 0)
                                                                <span class="text-xs text-amber-600 dark:text-amber-400 block">
                                                                    {{ $pendingItems }} pending
                                                                </span>
                                                            @endif
                                                            @if($diagnosticItems > 0)
                                                                <span class="text-xs text-blue-600 dark:text-blue-400 block">
                                                                    {{ $diagnosticItems }} need diagnosis
                                                                </span>
                                                            @endif
                                                            @if($repairItems > 0)
                                                                <span class="text-xs text-red-600 dark:text-red-400 block">
                                                                    {{ $repairItems }} need repair
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        @php
                                                            $items = $inspection->itemResults->where('vendor_id', auth()->user()->vendor->id);
                                                            $hasUrgent = $items->contains(function($item) {
                                                                return $item->status === 'fail' || 
                                                                    ($item->requires_repair && !$item->repair_completed);
                                                            });
                                                            $hasDiagnostic = $items->whereNotNull('diagnostic_status')->count() > 0;
                                                            $hasPending = $items->where('status', 'pending')->count() > 0;
                                                            $hasWarning = $items->where('status', 'warning')->count() > 0;
                                                        @endphp
                                                        
                                                        @if($hasUrgent)
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                                Urgent
                                                            </span>
                                                        @elseif($hasDiagnostic)
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                                Needs Diagnosis
                                                            </span>
                                                        @elseif($hasWarning)
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200">
                                                                Warning
                                                            </span>
                                                        @elseif($hasPending)
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200">
                                                                Pending
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                                            {{ $inspection->created_at->diffForHumans() }}
                                                        </div>
                                                        @if($inspection->created_at->diffInDays() > 2)
                                                            <div class="text-xs text-red-600 dark:text-red-400 font-medium">
                                                                Urgent Attention Needed
                                                            </div>
                                                        @endif
                                                    </td>
                                                    <td class="px-6 py-4 text-right">
                                                        <a href="{{ route('vendor.inspections.show', $inspection) }}" 
                                                           class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-white hover:bg-zinc-50 text-zinc-900 rounded-lg border border-zinc-200">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                            </svg>
                                                            View Details
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-zinc-950 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-800">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">Recent Activity</h3>
                            
                            @if($recentActivity->isEmpty())
                                <p class="text-zinc-500 text-center py-4">No recent activity</p>
                            @else
                                <div class="flow-root">
                                    <ul role="list" class="-mb-8">
                                        @foreach($recentActivity as $activity)
                                            <li>
                                                <div class="relative pb-8">
                                                    @if(!$loop->last)
                                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-zinc-200 dark:bg-zinc-800" aria-hidden="true"></span>
                                                    @endif
                                                    <div class="relative flex space-x-3">
                                                        <div>
                                                            @switch($activity['status'])
                                                                @case('completed')
                                                                    <span class="h-8 w-8 rounded-full bg-zinc-950 flex items-center justify-center ring-8 ring-white dark:ring-zinc-950">
                                                                        <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                        </svg>
                                                                    </span>
                                                                    @break
                                                                @case('pending_approval')
                                                                    <span class="h-8 w-8 rounded-full bg-zinc-950 flex items-center justify-center ring-8 ring-white dark:ring-zinc-950">
                                                                        <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                        </svg>
                                                                    </span>
                                                                    @break
                                                                @default
                                                                    <span class="h-8 w-8 rounded-full bg-zinc-950 flex items-center justify-center ring-8 ring-white dark:ring-zinc-950">
                                                                        <svg class="h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                                        </svg>
                                                                    </span>
                                                            @endswitch
                                                        </div>
                                                        <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                            <div>
                                                                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                                                    {{ ucfirst($activity['status']) }} inspection for 
                                                                    <a href="{{ route('vendor.inspections.show', $activity['inspection']) }}" class="font-medium text-zinc-900 dark:text-zinc-100">
                                                                        {{ $activity['inspection']->vehicle->year }} {{ $activity['inspection']->vehicle->make }}
                                                                    </a>
                                                                </p>
                                                            </div>
                                                            <div class="text-right text-sm whitespace-nowrap text-zinc-500 dark:text-zinc-400">
                                                                <time datetime="{{ $activity['date'] }}">{{ $activity['date']->diffForHumans() }}</time>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recently Completed Inspections -->
            <div class="mt-6">
                <div class="bg-white dark:bg-zinc-950 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-800">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100">Recently Completed</h3>
                            <a href="{{ route('vendor.inspection-history') }}" 
                               class="inline-flex items-center px-4 py-2 border border-zinc-200 dark:border-zinc-800 text-sm font-medium rounded-md text-zinc-900 dark:text-zinc-100 bg-white dark:bg-zinc-950 hover:bg-zinc-50 dark:hover:bg-zinc-900">
                                View All History
                                <svg class="ml-2 -mr-1 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                        
                        @if($completedInspections->isEmpty())
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-zinc-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-zinc-900 dark:text-zinc-100">No completed inspections</h3>
                                <p class="mt-1 text-sm text-zinc-500">Completed inspections will appear here</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                                    <thead class="bg-zinc-50 dark:bg-zinc-900">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Vehicle</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Items</th>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Completion Date</th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-zinc-950 divide-y divide-zinc-200 dark:divide-zinc-800">
                                        @foreach($completedInspections->take(5) as $inspection)
                                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900">
                                                <td class="px-6 py-4">
                                                    <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                        {{ $inspection->vehicle->year }} {{ $inspection->vehicle->make }} {{ $inspection->vehicle->model }}
                                                    </div>
                                                    <div class="text-sm text-zinc-500">
                                                        VIN: {{ $inspection->vehicle->vin }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                                        {{ $inspection->inspectionItems->where('vendor_id', auth()->id())->count() }} items
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4">
                                                    <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                                        {{ $inspection->completed_date?->format('M d, Y') }}
                                                    </div>
                                                    <div class="text-sm text-zinc-500">
                                                        {{ $inspection->completed_date?->diffForHumans() }}
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 text-right">
                                                    <a href="{{ route('vendor.inspections.show', $inspection) }}" 
                                                       class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-medium bg-white hover:bg-zinc-50 text-zinc-900 rounded-lg border border-zinc-200">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                        </svg>
                                                        View Details
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 