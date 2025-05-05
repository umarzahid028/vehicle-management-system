<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-tight">Transport Details</h2>
                <p class="text-sm text-muted-foreground">View and manage transport information.</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('transports.index') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                    <x-heroicon-o-arrow-left class="mr-2 h-4 w-4" />
                    Back to Transports
                </a>
                <a href="{{ route('transports.edit', $transport) }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                    <x-heroicon-o-pencil class="mr-2 h-4 w-4" />
                    Edit Transport
                </a>
            </div>
        </div>
    </x-slot>

    <div class="container mx-auto space-y-6">
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif
        
        <!-- Batch Information -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Batch Information</h3>
                    <a href="{{ route('transports.batch', ['batchId' => $transport->batch_id]) }}" class="text-indigo-600 hover:text-indigo-900 text-sm flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        View All Vehicles in Batch
                    </a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <p class="text-sm font-medium text-gray-500">Batch ID</p>
                        <p class="font-semibold">{{ $transport->batch_id }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Batch Name</p>
                        <p>{{ $transport->batch_name ?: 'Not specified' }}</p>
                    </div>
                    @if($transport->qr_code_path)
                        <div>
                            <p class="text-sm font-medium text-gray-500">QR Code</p>
                            <div class="mt-1">
                                <img src="{{ asset('storage/' . $transport->qr_code_path) }}" alt="Batch QR Code" class="h-16 w-16">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Vehicle Information -->
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Vehicle Information</h3>
                        
                        @if ($transport->vehicle)
                            <div class="mb-6">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="flex-1">
                                        <h4 class="text-xl font-bold">
                                            {{ $transport->vehicle->year }} {{ $transport->vehicle->make }} {{ $transport->vehicle->model }}
                                        </h4>
                                        <p class="text-gray-500">Stock #: {{ $transport->vehicle->stock_number }}</p>
                                    </div>
                                    <div>
                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            {{ $transport->vehicle->status == 'available' ? 'bg-green-100 text-green-800' : '' }}
                                            {{ $transport->vehicle->status == 'sold' ? 'bg-red-100 text-red-800' : '' }}
                                            {{ $transport->vehicle->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                            {{ $transport->vehicle->status == 'in_transit' ? 'bg-blue-100 text-blue-800' : '' }}
                                        ">
                                            {{ ucfirst($transport->vehicle->status) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">VIN</p>
                                        <p>{{ $transport->vehicle->vin ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Exterior Color</p>
                                        <p>{{ $transport->vehicle->exterior_color ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Advertising Price</p>
                                        <p>${{ number_format($transport->vehicle->advertising_price ?? 0, 2) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Date In Stock</p>
                                        <p>{{ $transport->vehicle->date_in_stock ? $transport->vehicle->date_in_stock->format('M d, Y') : 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <a href="{{ route('vehicles.show', $transport->vehicle) }}">
                                    <x-shadcn.button variant="outline" size="sm">
                                        View Full Vehicle Details
                                    </x-shadcn.button>
                                </a>
                            </div>
                        @else
                            <div class="p-4 bg-red-100 text-red-700 rounded">
                                Vehicle not found or has been deleted.
                            </div>
                        @endif
                    </div>
                    
                    <!-- Transport Details -->
                    <div class="bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Transport Details</h3>
                        
                        <div class="mb-6">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-xl font-bold">
                                    Transport #{{ $transport->id }}
                                </h4>
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    {{ $transport->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $transport->status == 'in_transit' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $transport->status == 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $transport->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                ">
                                    {{ ucfirst($transport->status) }}
                                </span>
                            </div>
                            
                            <!-- Status Update Form for Transporters -->
                            @if(auth()->user()->hasRole('Transporter'))
                                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                                    <h5 class="font-semibold mb-4">Update Transport Status</h5>
                                    <form action="{{ route('transports.update-status', $transport) }}" method="POST">
                                        @csrf
                                        <div class="space-y-4">
                                            <div>
                                                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                                                <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                                    <option value="">Select Status</option>
                                                    @if($transport->status === 'pending')
                                                        <option value="picked_up">Mark as Picked Up</option>
                                                    @elseif($transport->status === 'picked_up')
                                                        <option value="delivered">Mark as Delivered</option>
                                                    @endif
                                                </select>
                                            </div>
                                            
                                            <div id="pickupDateField" class="hidden">
                                                <label for="pickup_date" class="block text-sm font-medium text-gray-700">Pickup Date</label>
                                                <input type="date" name="pickup_date" id="pickup_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            </div>
                                            
                                            <div id="deliveryDateField" class="hidden">
                                                <label for="delivery_date" class="block text-sm font-medium text-gray-700">Delivery Date</label>
                                                <input type="date" name="delivery_date" id="delivery_date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            </div>
                                            
                                            <div class="flex justify-end">
                                                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                    Update Status
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- JavaScript for form fields visibility -->
                                <script>
                                    document.getElementById('status').addEventListener('change', function() {
                                        const pickupDateField = document.getElementById('pickupDateField');
                                        const deliveryDateField = document.getElementById('deliveryDateField');
                                        
                                        if (this.value === 'in_transit') {
                                            pickupDateField.classList.remove('hidden');
                                            deliveryDateField.classList.add('hidden');
                                        } else if (this.value === 'delivered') {
                                            pickupDateField.classList.add('hidden');
                                            deliveryDateField.classList.remove('hidden');
                                        } else {
                                            pickupDateField.classList.add('hidden');
                                            deliveryDateField.classList.add('hidden');
                                        }
                                    });
                                </script>
                            @endif
                            
                            <div class="grid grid-cols-1 gap-4 mb-6">
                                <div class="bg-gray-50 p-4 rounded">
                                    <h5 class="font-semibold mb-2">Route Information</h5>
                                    <div class="flex items-center">
                                        @if ($transport->origin)
                                            <span class="font-medium">{{ $transport->origin }}</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mx-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        @endif
                                        <span class="font-medium">{{ $transport->destination }}</span>
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Pickup Date</p>
                                        <p>{{ $transport->pickup_date ? $transport->pickup_date->format('M d, Y') : 'Not set' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-500">Delivery Date</p>
                                        <p>{{ $transport->delivery_date ? $transport->delivery_date->format('M d, Y') : 'Not set' }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Gate Pass Information -->
                            @if($transport->hasGatePass())
                            <div class="bg-gray-50 p-4 rounded mb-4">
                                <h5 class="font-semibold mb-2">Gate Pass</h5>
                                <div class="flex items-center">
                                    <a href="{{ $transport->getGatePassUrl() }}" target="_blank" class="inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        View Gate Pass
                                    </a>
                                    <a href="{{ $transport->getGatePassUrl() }}" download class="inline-flex items-center px-3 py-2 ml-2 text-sm font-medium text-indigo-700 bg-indigo-100 rounded-md hover:bg-indigo-200">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Download
                                    </a>
                                </div>
                            </div>
                            @endif
                            
                            <div class="bg-gray-50 p-4 rounded mb-4">
                                <h5 class="font-semibold mb-2">Transporter Information</h5>
                                @if ($transport->transporter)
                                    <p><span class="font-medium">Name:</span> {{ $transport->transporter->name }}</p>
                                    @if ($transport->transporter->contact_person)
                                        <p><span class="font-medium">Contact Person:</span> {{ $transport->transporter->contact_person }}</p>
                                    @endif
                                    @if ($transport->transporter->phone)
                                        <p><span class="font-medium">Phone:</span> {{ $transport->transporter->phone }}</p>
                                    @endif
                                    @if ($transport->transporter->email)
                                        <p><span class="font-medium">Email:</span> {{ $transport->transporter->email }}</p>
                                    @endif
                                    <div class="mt-2">
                                        <a href="{{ route('transporters.show', $transport->transporter) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                            View Full Transporter Details
                                        </a>
                                    </div>
                                @elseif ($transport->transporter_name)
                                    <p><span class="font-medium">Name:</span> {{ $transport->transporter_name }}</p>
                                    @if ($transport->transporter_phone)
                                        <p><span class="font-medium">Phone:</span> {{ $transport->transporter_phone }}</p>
                                    @endif
                                    @if ($transport->transporter_email)
                                        <p><span class="font-medium">Email:</span> {{ $transport->transporter_email }}</p>
                                    @endif
                                @else
                                    <p class="text-gray-500">No transporter assigned</p>
                                @endif
                            </div>
                            
                            @if ($transport->notes)
                                <div class="bg-gray-50 p-4 rounded">
                                    <h5 class="font-semibold mb-2">Notes</h5>
                                    <p class="whitespace-pre-line">{{ $transport->notes }}</p>
                                </div>
                            @endif
                        </div>
                        
                        <div class="mt-4 text-xs text-gray-500">
                            <p>Created: {{ $transport->created_at->format('M d, Y h:i A') }}</p>
                            <p>Last Updated: {{ $transport->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>
                
                <!-- Delete Transport Button -->
                @if(!auth()->user()->hasRole('Transporter'))
                    <div class="mt-6 flex justify-end">
                        <form action="{{ route('transports.destroy', $transport) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this transport?');">
                            @csrf
                            @method('DELETE')
                            <x-shadcn.button type="submit" variant="destructive" class="bg-red-600 hover:bg-red-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Delete Transport
                            </x-shadcn.button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout> 