<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Vehicle Details') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('vehicles.edit', $vehicle) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-900 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    {{ __('Edit Vehicle') }}
                </a>
                <a href="{{ route('vehicles.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Vehicle Image -->
                        <div class="bg-white p-6 rounded-lg shadow-md flex justify-center items-center">
                            <img src="{{ $vehicle->image_url }}" alt="{{ $vehicle->stock_number }}" 
                                class="max-h-80 object-contain rounded-lg" id="mainImage">
                        </div>
                       
                        <!-- Basic Information -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Stock Number</p>
                                    <p class="mt-1">{{ $vehicle->stock_number }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">VIN</p>
                                    <p class="mt-1">{{ $vehicle->vin }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Year</p>
                                    <p class="mt-1">{{ $vehicle->year }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Make</p>
                                    <p class="mt-1">{{ $vehicle->make }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Model</p>
                                    <p class="mt-1">{{ $vehicle->model }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Trim</p>
                                    <p class="mt-1">{{ $vehicle->trim ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Status</p>
                                    <p class="mt-1">
                                        <span class="px-2 py-1 text-xs leading-5 font-semibold rounded-full 
                                            @if (in_array($vehicle->status, [$vehicle::STATUS_SOLD, $vehicle::STATUS_ARCHIVE]))
                                                bg-gray-500 text-white
                                            @elseif (in_array($vehicle->status, [$vehicle::STATUS_TRANSPORT_CANCELLED, $vehicle::STATUS_INSPECTION_CANCELLED, $vehicle::STATUS_REPAIR_CANCELLED]))
                                                bg-red-500 text-white
                                            @elseif (in_array($vehicle->status, [$vehicle::STATUS_TRANSPORT_COMPLETED, $vehicle::STATUS_INSPECTION_COMPLETED, $vehicle::STATUS_REPAIR_COMPLETED, $vehicle::STATUS_GOODWILL_CLAIMS_COMPLETED]))
                                                bg-green-500 text-white
                                            @elseif (in_array($vehicle->status, [$vehicle::STATUS_READY_FOR_SALE, $vehicle::STATUS_READY_FOR_SALE_ASSIGNED]))
                                                bg-blue-500 text-white
                                            @elseif (in_array($vehicle->status, [$vehicle::STATUS_TRANSPORT_IN_PROGRESS, $vehicle::STATUS_INSPECTION_IN_PROGRESS, $vehicle::STATUS_REPAIR_IN_PROGRESS, $vehicle::STATUS_TRANSPORT_IN_TRANSIT]))
                                                bg-yellow-500 text-white
                                            @elseif ($vehicle->status === $vehicle::STATUS_AVAILABLE)
                                                bg-indigo-500 text-white
                                            @else
                                                bg-purple-500 text-white
                                            @endif">
                                            {{ $vehicle->status }}
                                        </span>
                                    </p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Price</p>
                                    <p class="mt-1">${{ number_format($vehicle->advertising_price, 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Vehicle Gallery -->
                        <div class="md:col-span-2 bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Vehicle Gallery</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                @if($vehicle->has_main_image)
                                <div class="cursor-pointer group">
                                    <div class="relative aspect-square overflow-hidden rounded-lg bg-gray-100">
                                        <img src="{{ $vehicle->image_url }}" alt="{{ $vehicle->stock_number }} Main" 
                                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                                            onclick="setMainImage('{{ $vehicle->image_url }}', '{{ $vehicle->stock_number }} Main')">
                                        <div class="absolute inset-0 bg-black/5 group-hover:bg-black/20 transition-colors duration-300"></div>
                                        <div class="absolute top-2 left-2">
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-amber-100 text-amber-800">
                                                Main
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                
                                @forelse($vehicle->images as $image)
                                <div class="cursor-pointer group">
                                    <div class="relative aspect-square overflow-hidden rounded-lg bg-gray-100">
                                        <img src="{{$image->image_url }}" alt="{{ $vehicle->stock_number }} Image {{ $loop->iteration }}" 
                                            class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-110"
                                            onclick="setMainImage('{{$image->image_url }}', '{{ $vehicle->stock_number }} Image {{ $loop->iteration }}')">
                                        <div class="absolute inset-0 bg-black/5 group-hover:bg-black/20 transition-colors duration-300"></div>
                                        @if($image->is_featured)
                                        <div class="absolute top-2 left-2">
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-md bg-blue-100 text-blue-800">
                                                Featured
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @empty
                                @if(!$vehicle->has_main_image)
                                <div class="md:col-span-4 text-center py-6 bg-gray-50 rounded-lg">
                                    <p class="text-gray-500">No images available for this vehicle.</p>
                                </div>
                                @endif
                                @endforelse
                            </div>
                        </div>

                        <!-- Vehicle Status Manager -->
                        <div class="md:col-span-2">
                            <x-vehicle-status-manager :vehicle="$vehicle" />
                        </div>

                        <!-- Additional Details -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Additional Details</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Odometer</p>
                                    <p class="mt-1">{{ number_format($vehicle->odometer) ?? 'N/A' }} miles</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Exterior Color</p>
                                    <p class="mt-1">{{ $vehicle->exterior_color ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Interior Color</p>
                                    <p class="mt-1">{{ $vehicle->interior_color ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Body Type</p>
                                    <p class="mt-1">{{ $vehicle->body_type ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Drive Train</p>
                                    <p class="mt-1">{{ $vehicle->drive_train ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Engine</p>
                                    <p class="mt-1">{{ $vehicle->engine ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Transmission</p>
                                    <p class="mt-1">{{ $vehicle->transmission ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Fuel Type</p>
                                    <p class="mt-1">{{ $vehicle->fuel_type ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Acquisition Information -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Acquisition Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Date in Stock</p>
                                    <p class="mt-1">{{ $vehicle->date_in_stock ? $vehicle->date_in_stock->format('M d, Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Purchased From</p>
                                    <p class="mt-1">{{ $vehicle->purchased_from ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Purchase Date</p>
                                    <p class="mt-1">{{ $vehicle->purchase_date ? $vehicle->purchase_date->format('M d, Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Purchase Source</p>
                                    <p class="mt-1">{{ $vehicle->vehicle_purchase_source ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Sales Information -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Sales Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Deal Status</p>
                                    <p class="mt-1">{{ $vehicle->deal_status ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Sold Date</p>
                                    <p class="mt-1">{{ $vehicle->sold_date ? $vehicle->sold_date->format('M d, Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Buyer Name</p>
                                    <p class="mt-1">{{ $vehicle->buyer_name ?? 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-500">Featured</p>
                                    <p class="mt-1">{{ $vehicle->is_featured ? 'Yes' : 'No' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inspection & Repair Section -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold">Inspection & Repair</h3>
                        <div class="flex space-x-2">
                            @if($vehicle->status === 'arrived' || $vehicle->transport_status === 'delivered')
                                <a href="/inspection/vehicles/{{ $vehicle->id }}/comprehensive" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    <x-heroicon-o-clipboard-document-list class="h-4 w-4 mr-1" />
                                    Start Inspection
                                </a>
                            @else
                                <div class="text-sm text-gray-500">
                                    @if($vehicle->transport_status !== 'delivered')
                                        Vehicle must be delivered by transporter to start an inspection
                                    @elseif($vehicle->status === 'ready' || $vehicle->status === \App\Models\Vehicle::STATUS_REPAIR_COMPLETED)
                                        @if($vehicle->status === \App\Models\Vehicle::STATUS_REPAIR_COMPLETED)
                                            Vehicle has completed all repairs
                                        @else
                                            Vehicle has completed all inspections
                                        @endif

                                        @hasanyrole('Admin|Sales Manager|Recon Manager')
                                            @php
                                                $latestInspection = $vehicle->vehicleInspections()->where('status', 'completed')->latest()->first();
                                                $needsRepairItems = 0;
                                                
                                                if ($latestInspection) {
                                                    $needsRepairItems = $latestInspection->itemResults()
                                                        ->where('requires_repair', true)
                                                        ->where('repair_completed', false)
                                                        ->count();
                                                }
                                            @endphp
                                            
                                            @if($latestInspection && $needsRepairItems === 0)
                                                <form action="{{ route('inspection.inspections.assign-to-sales', $latestInspection) }}" method="POST" class="inline ml-4">
                                                    @csrf
                                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                        <x-heroicon-o-user-plus class="h-4 w-4 mr-1" />
                                                        Assign to Sales Team
                                                    </button>
                                                </form>
                                            @endif
                                        @endhasanyrole
                                    @else
                                        Vehicle is not ready for inspection
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Inspection Stages -->
                    @if($vehicle->vehicleInspections && $vehicle->vehicleInspections->count() > 0)
                        <div class="mt-4">
                            <h4 class="text-md font-medium mb-3">Inspection History</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stage</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Assigned To</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($vehicle->vehicleInspections as $inspection)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm font-medium text-gray-900">{{ $inspection->inspectionStage->name }}</div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">{{ $inspection->inspection_date ? $inspection->inspection_date->format('M d, Y') : 'Not started' }}</div>
                                                    @if($inspection->completed_date)
                                                        <div class="text-xs text-gray-500">Completed: {{ $inspection->completed_date->format('M d, Y') }}</div>
                                                    @endif
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $inspection->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                           ($inspection->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                        {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                                                    </span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap">
                                                    <div class="text-sm text-gray-900">
                                                        {{ $inspection->user->name }}
                                                        @if($inspection->vendor)
                                                            <div class="text-xs text-gray-500">Vendor: {{ $inspection->vendor->name }}</div>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <div class="flex space-x-2">
                                                        <a href="{{ route('inspection.inspections.show', $inspection) }}" class="text-indigo-600 hover:text-indigo-900" title="View Details">
                                                            <x-heroicon-o-eye class="h-5 w-5" />
                                                        </a>
                                                        @if($inspection->status !== 'completed' && $vehicle->status === 'arrived')
                                                            <a href="{{ route('inspection.inspections.edit', $inspection) }}" class="text-indigo-600 hover:text-indigo-900" title="Continue Inspection">
                                                                <x-heroicon-o-clipboard-document-check class="h-5 w-5" />
                                                            </a>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-6 bg-gray-50 rounded-lg">
                            <p class="text-gray-500">No inspections have been performed on this vehicle.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
    <script>
        function setMainImage(src, alt) {
            const mainImage = document.getElementById('mainImage');
            mainImage.src = src;
            mainImage.alt = alt;
            
            // Smooth scroll to main image on mobile
            if (window.innerWidth < 768) {
                document.getElementById('mainImage').scrollIntoView({ behavior: 'smooth' });
            }
        }
    </script>
    @endsection
</x-app-layout> 