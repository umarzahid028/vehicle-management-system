<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Assigned Inspections') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($assignedInspections->isEmpty())
                        <div class="text-center py-8">
                            <x-heroicon-o-clipboard-document-check class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No Assigned Inspections</h3>
                            <p class="mt-1 text-sm text-gray-500">You don't have any pending inspections at the moment.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto -mx-4 sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Vehicle</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Items</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Status</th>
                                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Assigned Date</th>
                                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                                    <span class="sr-only">Actions</span>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            @foreach($assignedInspections as $inspection)
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
                                                            $vendorItems = $inspection->itemResults->where('status', '!=', 'pass')->where('vendor_id', auth()->user()->vendor->id);
                                                            $totalItems = $vendorItems->count();
                                                            $completedItems = $vendorItems->whereIn('status', ['completed', 'cancelled'])->count();
                                                            $progressPercentage = $totalItems > 0 ? ($completedItems / $totalItems * 100) : 0;
                                                        @endphp
                                                        <div class="font-medium text-gray-900">
                                                            {{ $completedItems }} / {{ $totalItems }} completed
                                                        </div>
                                                        <div class="mt-2 flex items-center">
                                                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                                                <div class="h-2.5 rounded-full {{ $progressPercentage == 100 ? 'bg-green-600' : 'bg-blue-600' }}" 
                                                                     style="width: {{ $progressPercentage }}%"></div>
                                                            </div>
                                                            <span class="ml-2 text-xs text-gray-500">{{ number_format($progressPercentage) }}%</span>
                                                        </div>
                                                    </td>
                                                    <td class="px-3 py-4 text-sm">
                                                        @php
                                                            $filteredItems = $vendorItems;
                                                            $allCompleted = $filteredItems->count() > 0 && $filteredItems->every(function($item) {
                                                                return in_array($item->status, ['completed', 'cancelled']);
                                                            });
                                                            $allCancelled = $filteredItems->count() > 0 && $filteredItems->every(function($item) {
                                                                return $item->status === 'cancelled';
                                                            });
                                                        @endphp
                                                        <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                            {{ $allCancelled ? 'bg-gray-100 text-gray-800' : 
                                                              ($allCompleted ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                            {{ $allCancelled ? 'Cancelled' : ($allCompleted ? 'Completed' : 'In Progress') }}
                                                        </span>
                                                    </td>
                                                    <td class="px-3 py-4 text-sm text-gray-500">
                                                        {{ $inspection->created_at->format('M d, Y') }}
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