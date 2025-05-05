<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inspection History') }}
            </h2>
            <a href="{{ route('vendor.dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                Back to Dashboard
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg w-full">
                <div class="p-6 w-full">
                    @if($completedInspections->isEmpty())
                        <div class="text-center py-12">
                            <x-heroicon-o-clipboard-document-check class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Completed Inspections</h3>
                            <p class="mt-1 text-sm text-gray-500">Your completed inspections will appear here.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto w-full">
                            <div class="w-full align-middle">
                                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg w-full">
                                    <table class="w-full divide-y divide-gray-300 table-fixed">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6 w-1/4">Vehicle</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 w-1/4">Items</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 w-1/6">Status</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 w-1/5">Completed</th>
                                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6 w-1/6">
                                                    <span class="sr-only">Actions</span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            @foreach($completedInspections as $inspection)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="py-4 pl-4 pr-3 text-sm sm:pl-6">
                                                        <div class="font-medium text-gray-900">
                                                            {{ $inspection->vehicle->year }} {{ $inspection->vehicle->make }} {{ $inspection->vehicle->model }}
                                                        </div>
                                                        <div class="mt-1 text-gray-500">
                                                            <div>Stock #: {{ $inspection->vehicle->stock_number }}</div>
                                                            <div class="truncate max-w-xs">VIN: {{ $inspection->vehicle->vin }}</div>
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-4 text-sm">
                                                        @php
                                                            $totalItems = $inspection->itemResults->where('status', '!=', 'pass')->count();
                                                            $completedItems = $inspection->itemResults->whereIn('status', ['completed'])->count();
                                                            $cancelledItems = $inspection->itemResults->whereIn('status', ['cancelled'])->count();
                                                        @endphp
                                                        <div class="font-medium text-gray-900">
                                                            {{ $totalItems }} {{ Str::plural('item', $totalItems) }}
                                                        </div>
                                                        <div class="mt-1 text-xs text-gray-500">
                                                            <div class="flex items-center gap-1">
                                                                <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                                                <span>{{ $completedItems }} completed</span>
                                                            </div>
                                                            @if($cancelledItems > 0)
                                                            <div class="flex items-center gap-1 mt-0.5">
                                                                <span class="h-2 w-2 rounded-full bg-gray-500"></span>
                                                                <span>{{ $cancelledItems }} cancelled</span>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-4 text-sm">
                                                        @php
                                                            $completed = $inspection->itemResults->where('status', 'completed')->count();
                                                            $cancelled = $inspection->itemResults->where('status', 'cancelled')->count();
                                                            $total = $inspection->itemResults->where('status', '!=', 'pass')->count();
                                                            
                                                            $allCompleted = $completed === $total && $total > 0;
                                                            $allCancelled = $cancelled === $total && $total > 0;
                                                        @endphp
                                                        
                                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            {{ $allCancelled ? 'bg-gray-100 text-gray-800' : 
                                                              ($allCompleted ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                            {{ $allCancelled ? 'Cancelled' : ($allCompleted ? 'Completed' : 'Mixed') }}
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-4 text-sm text-gray-500">
                                                        {{ $inspection->completed_date?->format('M d, Y') }}
                                                        <div class="text-xs text-gray-400">
                                                            {{ $inspection->completed_date?->format('h:ia') }}
                                                        </div>
                                                    </td>
                                                    <td class="relative py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                        <a href="{{ route('vendor.inspections.show', $inspection) }}" 
                                                           class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            View
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 