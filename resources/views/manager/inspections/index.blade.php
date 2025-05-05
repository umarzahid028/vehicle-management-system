<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-zinc-800 dark:text-zinc-200">
                {{ __('Vehicle Inspections') }}
            </h2>
            <a href="{{ route('manager.inspections.create') }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-zinc-900 rounded-md hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-500">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                New Inspection
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white dark:bg-zinc-950 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-800">
                <div class="p-6">
                    <!-- Filters -->
                    <div class="mb-6">
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <label for="status" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Filter by Status</label>
                                <select id="status" name="status" class="block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm focus:border-zinc-500 focus:ring-zinc-500">
                                    <option value="">All Statuses</option>
                                    <option value="pending">Pending</option>
                                    <option value="in_progress">In Progress</option>
                                    <option value="completed">Completed</option>
                                    <option value="needs_attention">Needs Attention</option>
                                </select>
                            </div>
                            <div class="flex-1">
                                <label for="date" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Filter by Date</label>
                                <select id="date" name="date" class="block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm focus:border-zinc-500 focus:ring-zinc-500">
                                    <option value="">All Time</option>
                                    <option value="today">Today</option>
                                    <option value="week">This Week</option>
                                    <option value="month">This Month</option>
                                </select>
                            </div>
                            <div class="flex-1">
                                <label for="search" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2">Search</label>
                                <input type="text" id="search" name="search" placeholder="Search VIN, Make, Model..." 
                                       class="block w-full rounded-md border-zinc-300 dark:border-zinc-700 dark:bg-zinc-900 shadow-sm focus:border-zinc-500 focus:ring-zinc-500">
                            </div>
                        </div>
                    </div>

                    <!-- Inspections Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-zinc-200 dark:divide-zinc-800">
                            <thead class="bg-zinc-50 dark:bg-zinc-900">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Vehicle</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Vendors</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Last Update</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-zinc-950 divide-y divide-zinc-200 dark:divide-zinc-800">
                                @forelse($inspections as $inspection)
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
                                            @php
                                                $statusColor = match($inspection->status) {
                                                    'completed' => 'green',
                                                    'in_progress' => 'blue',
                                                    'needs_attention' => 'red',
                                                    default => 'zinc'
                                                };
                                            @endphp
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $statusColor }}-100 dark:bg-{{ $statusColor }}-900 text-{{ $statusColor }}-800 dark:text-{{ $statusColor }}-200">
                                                {{ str_replace('_', ' ', ucfirst($inspection->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                                {{ $inspection->vendors->count() }} Assigned
                                            </div>
                                            <div class="text-sm text-zinc-500">
                                                {{ $inspection->completedVendors->count() }} Completed
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-zinc-900 dark:text-zinc-100">
                                                {{ $inspection->updated_at->format('M d, Y') }}
                                            </div>
                                            <div class="text-sm text-zinc-500">
                                                {{ $inspection->updated_at->diffForHumans() }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <a href="{{ route('manager.inspections.show', $inspection) }}" 
                                               class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-zinc-900 bg-white rounded-lg border border-zinc-300 hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-500">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-sm text-zinc-500">
                                            No inspections found
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($inspections->hasPages())
                        <div class="mt-4">
                            {{ $inspections->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 