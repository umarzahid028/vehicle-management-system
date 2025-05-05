<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Sales Team Dashboard') }}
            </h2>
        </div>
    </x-slot>

    <div class="container mx-auto space-y-6">
        <!-- Stats Section -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Ready for Sale -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Ready for Sale</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_ready'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- With Issues -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-yellow-100">
                            <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">With Issues</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_with_issues'] }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sold -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100">
                            <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Sold</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $stats['total_sold'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicles List -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Vehicles Ready for Sale</h3>
                
                @if($readyVehicles->isEmpty())
                    <p class="text-gray-500 text-center py-4">No vehicles are currently ready for sale.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Issues</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Last Inspection</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($readyVehicles as $vehicle)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($vehicle->photos->isNotEmpty())
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ $vehicle->photos->first()->thumbnail_url }}" alt="{{ $vehicle->full_name }}">
                                                    </div>
                                                @endif
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $vehicle->full_name }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        VIN: {{ $vehicle->vin }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                {{ $vehicle->status }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $vehicle->salesIssues->whereNull('resolved_at')->count() }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $vehicle->inspections->sortByDesc('created_at')->first()?->created_at?->diffForHumans() ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('vehicles.show', $vehicle) }}" class="text-indigo-600 hover:text-indigo-900">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $readyVehicles->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout> 