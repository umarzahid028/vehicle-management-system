<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Assigned Vehicles') }}
            </h2>
            <div class="flex items-center space-x-2 text-sm text-gray-500">
                <svg class="h-5 w-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>{{ $vehicles->count() }} vehicles assigned to you</span>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded shadow-sm" role="alert">
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            @endif

            @if($vehicles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($vehicles as $vehicle)
                        <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 overflow-hidden border border-gray-100 flex flex-col h-full">
                            <!-- Vehicle Image -->
                            <div class="relative">
                                @if($vehicle->getImageUrlAttribute())
                                    <img src="{{ $vehicle->getImageUrlAttribute() }}" alt="{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}" class="w-full h-56 object-cover">
                                @else
                                    <div class="w-full h-56 bg-gray-100 flex items-center justify-center">
                                        <svg class="h-24 w-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                                        </svg>
                                    </div>
                                @endif
                                
                                <!-- Status Badge -->
                                <div class="absolute top-4 left-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $vehicle->status === 'Sold' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        @if($vehicle->status === 'Sold')
                                            <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        @else
                                            <svg class="h-3 w-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                            </svg>
                                        @endif
                                        {{ $vehicle->status }}
                                    </span>
                                </div>
                                
                                <!-- Stock Number Badge -->
                                <div class="absolute top-4 right-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Stock #{{ $vehicle->stock_number }}
                                    </span>
                                </div>
                            </div>

                            <!-- Vehicle Details -->
                            <div class="p-5 flex-grow">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-lg font-bold text-gray-900">{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}</h3>
                                </div>
                                
                                <div class="mt-4 space-y-3">
                                    <!-- VIN -->
                                    <div class="flex items-center text-sm">
                                        <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"></path>
                                        </svg>
                                        <span class="text-gray-600 mr-1">VIN:</span>
                                        <span class="font-medium text-gray-900">{{ $vehicle->vin }}</span>
                                    </div>
                                    
                                    <!-- Price Info -->
                                    <div class="flex items-center text-sm">
                                        <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-gray-600 mr-1">List Price:</span>
                                        <span class="font-medium text-gray-900">${{ number_format($vehicle->advertising_price ?? 0, 2) }}</span>
                                    </div>
                                    
                                    @if($vehicle->status === 'Sold' && isset($vehicle->sale))
                                    <div class="flex items-center text-sm">
                                        <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        <span class="text-gray-600 mr-1">Sold Price:</span>
                                        <span class="font-medium text-gray-900">${{ number_format($vehicle->sale->amount ?? 0, 2) }}</span>
                                    </div>
                                    @endif
                                    
                                    <!-- Assigned Date -->
                                    <div class="flex items-center text-sm">
                                        <svg class="h-4 w-4 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-gray-600 mr-1">Assigned:</span>
                                        <span class="font-medium text-gray-900">{{ $vehicle->assigned_for_sale_at ? $vehicle->assigned_for_sale_at->format('M d, Y') : 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Footer -->
                            <div class="px-5 py-4 bg-gray-50 border-t border-gray-100">
                                @if($vehicle->status !== 'Sold')
                                    <a href="{{ route('sales-team.sales.create', ['vehicle_id' => $vehicle->id]) }}" 
                                       class="inline-flex w-full justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                        Record Sale
                                    </a>
                                @else
                                    <div class="flex justify-between">
                                        <span class="inline-flex items-center text-sm font-medium text-gray-500">
                                            <svg class="h-4 w-4 mr-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            Sold on {{ isset($vehicle->sale) && $vehicle->sale->sale_date ? $vehicle->sale->sale_date->format('M d, Y') : 'N/A' }}
                                        </span>
                                        
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6">
                    {{ $vehicles->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 flex flex-col items-center justify-center text-center">
                        <svg class="h-16 w-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No Vehicles Assigned</h3>
                        <p class="text-gray-500 max-w-md">You don't have any vehicles assigned to you. When a vehicle is ready for sale, it will appear here.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout> 