<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Offsite Vendor Inspections') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Assigned Offsite Vendor Tasks</h3>

                @if($itemsByVehicle->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-gray-500">No offsite vendor inspections found.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Vehicle
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Items
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Assigned Date
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($itemsByVehicle as $vehicleId => $items)
                                    @php
                                        $vendor = $items->first()->assignedVendor;
                                        $vehicle = $items->first()->vehicleInspection->vehicle;
                                       
                                        // Count items that are either completed or cancelled
                                        $completedCount = $items->whereIn('status', ['completed', 'cancelled'])->count();

                                        $totalCount = $items->count();
                                        $percentComplete = $totalCount > 0 ? ($completedCount / $totalCount) * 100 : 0;
                                        
                                        $mostRecentDate = $items->max('created_at');
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                Stock #: {{ $vehicle->stock_number }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                VIN: {{ $vehicle->vin }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $completedCount }} / {{ $totalCount }} completed
                                            </div>
                                            <div class="w-44 bg-gray-200 rounded-full h-2.5 mt-2">
                                                <div class="bg-green-600 h-2.5 rounded-full" style="width: {{ $percentComplete }}%"></div>
                                            </div>
                                            <div class="text-xs text-gray-500 mt-1">{{ number_format($percentComplete) }}%</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($percentComplete == 100)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                    All Complete
                                                </span>
                                            @elseif($percentComplete > 0)
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    In Progress ({{ $completedCount }}/{{ $totalCount }})
                                                </span>
                                            @else
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    Pending (0/{{ $totalCount }})
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $mostRecentDate ? $mostRecentDate->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('recon.offsite-inspections.show', $vehicle) }}" class="text-indigo-600 hover:text-indigo-900">
                                                <span class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    <x-heroicon-o-eye class="h-4 w-4 mr-1" />
                                                    View
                                                </span>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6">
                        {{ $offsiteInspectionItems->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout> 