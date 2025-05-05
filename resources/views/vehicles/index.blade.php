<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Vehicle Management') }}
            </h2>
            <div>
            <a href="{{ route('vehicles.create') }}">
                    <x-shadcn.button variant="default" class="shadow-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        {{ __('Add Vehicle') }}
                    </x-shadcn.button>
                </a>

        
            </div>
        </div>
    </x-slot>

    @push('scripts')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        // Initialize Pusher to listen for real-time events
        const enableRealtime = () => {
            console.log('Initializing Pusher with key: {{ env('PUSHER_APP_KEY') }}, cluster: {{ env('PUSHER_APP_CLUSTER') }}');
            
            // Enable Pusher logging - uncomment to debug
            Pusher.logToConsole = true;
            
            const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}',
                forceTLS: true,
                enabledTransports: ['ws', 'wss']
            });
            
            pusher.connection.bind('connected', function() {
                console.log('Successfully connected to Pusher');
            });
            
            pusher.connection.bind('error', function(err) {
                console.error('Pusher connection error:', err);
            });
            
            // Subscribe to the vehicles channel
            const channel = pusher.subscribe('vehicles');
            
            channel.bind('pusher:subscription_succeeded', function() {
                console.log('Successfully subscribed to vehicles channel');
            });
            
            channel.bind('pusher:subscription_error', function(error) {
                console.error('Error subscribing to vehicles channel:', error);
            });
            
            // Listen for vehicle.created events
            channel.bind('vehicle.created', function(data) {
                console.log('New vehicles notification received', data);
                
                // Play notification sound
                playNotificationSound();
                
                // Show notification toast
                showNotification(data.vehicleCount);
                
                // Update the badge in the sidebar if present
                const sidebarBadge = document.querySelector('.sidebar-vehicle-badge');
                if (sidebarBadge) {
                    const currentCount = parseInt(sidebarBadge.textContent || '0');
                    sidebarBadge.textContent = currentCount + data.vehicleCount;
                    sidebarBadge.classList.remove('hidden');
                }
                
                // Refresh the page if we're on the vehicles list
                if (window.location.pathname.includes('/vehicles') && !window.location.pathname.includes('/vehicles/')) {
                    // Wait 3 seconds before refreshing to allow the notification to be seen
                    setTimeout(() => {
                        window.location.reload();
                    }, 3000);
                }
            });
        };
        
        // Function to play notification sound
        const playNotificationSound = () => {
            console.log('Attempting to play notification sound');
            const audio = new Audio('/sounds/notification.mp3');
            audio.volume = 0.5;
            audio.play()
                .then(() => console.log('Sound played successfully'))
                .catch(e => console.error('Sound playback error:', e));
        };
        
        // Function to show notification toast
        const showNotification = (vehicleCount) => {
            const notification = document.createElement('div');
            notification.className = 'fixed bottom-4 right-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 rounded shadow-lg z-50';
            notification.innerHTML = `
                <div class="flex items-center">
                    <div class="py-1"><svg class="h-6 w-6 text-yellow-500 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg></div>
                    <div>
                        <p class="font-bold">${vehicleCount} new vehicle${vehicleCount > 1 ? 's' : ''} added!</p>
                        <p class="text-sm">Click to view</p>
                    </div>
                    <button class="ml-4" onclick="this.parentNode.parentNode.remove();">×</button>
                </div>
            `;
            
            // Make the notification clickable to go to the vehicles list
            notification.addEventListener('click', (e) => {
                if (e.target.tagName !== 'BUTTON') {
                    window.location.href = '{{ route('vehicles.index') }}?unread=true';
                }
            });
            
            document.body.appendChild(notification);
            
            // Auto remove after 10 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 10000);
        };
        
        document.addEventListener('DOMContentLoaded', function() {
            // Enable real-time notifications
            enableRealtime();
            
            // Check if notification should play (only for new vehicles and not already played)
            const newVehiclesCount = {{ isset($newVehicleCount) ? $newVehicleCount : 0 }};
            const soundPlayed = sessionStorage.getItem('vehicle_notification_played');
            
            if (newVehiclesCount > 0 && !soundPlayed) {
                // Play notification sound on first user interaction
                const playSound = () => {
                    playNotificationSound();
                    // Mark as played in this session
                    sessionStorage.setItem('vehicle_notification_played', 'true');
                    // Remove event listeners after playing
                    document.removeEventListener('click', playSound);
                };
                
                // Add event listener for first user interaction
                document.addEventListener('click', playSound, { once: true });
                
                // Show a notification about new vehicles
                if (newVehiclesCount > 0) {
                    showNotification(newVehiclesCount);
                }
            }
        });
    </script>
    @endpush

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <!-- Flash Messages -->
                    @if (session('success'))
                        <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Search and Filter -->
                    <div class="mb-6">
                        <form action="{{ route('vehicles.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
                            <div class="flex-1">
                                <label for="search" class="sr-only">Search</label>
                                <div class="relative rounded-md">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                        </svg>
                                    </div>
                                    <x-shadcn.input 
                                        type="text" 
                                        name="search" 
                                        id="search" 
                                        :value="request('search')" 
                                        placeholder="Search by Stock #, VIN, Make, or Model"
                                        class="pl-10" 
                                    />
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <input type="checkbox" name="unread" value="true" id="unread" class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4" {{ request('unread') === 'true' ? 'checked' : '' }}>
                                <label for="unread" class="text-sm text-gray-700">Show only unread</label>
                            </div>
                            <div class="w-full md:w-48">
                                <label for="status" class="sr-only">Status</label>
                                <select name="status" id="status" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">All Statuses</option>
                                    @foreach($statusOptions as $status)
                                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ $status }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-full md:w-48">
                                <label for="category" class="sr-only">Category</label>
                                <select name="category" id="category" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">All Categories</option>
                                    @foreach($categoryOptions as $value => $label)
                                        <option value="{{ $value }}" {{ request('category') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-full md:w-48">
                                <label for="filter" class="sr-only">Quick Filters</label>
                                <select name="filter" id="filter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Quick Filters</option>
                                    <option value="available" {{ request('filter') == 'available' ? 'selected' : '' }}>Available</option>
                                    <option value="transport" {{ request('filter') == 'transport' ? 'selected' : '' }}>In Transport</option>
                                    <option value="inspection" {{ request('filter') == 'inspection' ? 'selected' : '' }}>In Inspection</option>
                                    <option value="repair" {{ request('filter') == 'repair' ? 'selected' : '' }}>In Repair</option>
                                    <option value="sales" {{ request('filter') == 'sales' ? 'selected' : '' }}>In Sales</option>
                                    <option value="sold" {{ request('filter') == 'sold' ? 'selected' : '' }}>Sold</option>
                                    <option value="goodwill" {{ request('filter') == 'goodwill' ? 'selected' : '' }}>Goodwill Claims</option>
                                    <option value="archive" {{ request('filter') == 'archive' ? 'selected' : '' }}>Archived</option>
                                </select>
                            </div>
                            <div>
                                <x-shadcn.button type="submit" variant="default">
                                    Filter
                                </x-shadcn.button>
                                @if(request('search') || request('status') || request('category') || request('filter'))
                                    <a href="{{ route('vehicles.index') }}" class="ml-2">
                                        <x-shadcn.button type="button" variant="outline">
                                            Clear
                                        </x-shadcn.button>
                                    </a>
                                @endif
                            </div>
                        </form>
                    </div>

                    <!-- Row Highlighting Legend -->
                    @if(isset($newVehicleId) || isset($updatedVehicleId))
                    <div class="flex gap-4 mb-4 text-sm">
                        @if(isset($newVehicleId))
                        <div class="flex items-center">
                            <span class="w-4 h-4 mr-2 bg-green-50 border border-green-200"></span>
                            <span>Newly added vehicle</span>
                        </div>
                        @endif
                        
                        @if(isset($updatedVehicleId))
                        <div class="flex items-center">
                            <span class="w-4 h-4 mr-2 bg-yellow-50 border border-yellow-200"></span>
                            <span>Recently updated vehicle</span>
                        </div>
                        @endif
                        
                        <div class="flex items-center">
                            <span class="w-4 h-4 mr-2 bg-yellow-100 border border-yellow-300"></span>
                            <span>Unread vehicle</span>
                        </div>
                    </div>
                    @endif

                    <!-- Vehicles Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Image
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stock #
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Vehicle
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        VIN
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Price
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date Added
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($vehicles as $vehicle)
                                    <tr class="
                                        @if(isset($newVehicleId) && $vehicle->id == $newVehicleId) bg-green-50 
                                        @elseif(isset($updatedVehicleId) && $vehicle->id == $updatedVehicleId) bg-yellow-50
                                        @elseif(!$vehicle->isReadByUser()) bg-yellow-100
                                        @endif
                                        hover:bg-gray-50
                                    ">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <a href="{{ route('vehicles.show', $vehicle) }}" class="block">
                                                <img src="{{ $vehicle->image_url }}" alt="{{ $vehicle->stock_number }}" class="h-12 w-16 object-cover rounded hover:opacity-80 transition-opacity">
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <a href="{{ route('vehicles.show', $vehicle) }}" class="hover:text-indigo-600 hover:underline">
                                                {{ $vehicle->stock_number }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }} {{ $vehicle->trim }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $vehicle->vin }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
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
                                                {{ ucfirst(str_replace('_', ' ', $vehicle->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            ${{ number_format($vehicle->advertising_price, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $vehicle->date_in_stock ? $vehicle->date_in_stock->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <a href="{{ route('vehicles.show', $vehicle) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </a>
                                                
                                                @if(($vehicle->status === 'ready' || $vehicle->status === \App\Models\Vehicle::STATUS_REPAIR_COMPLETED) && auth()->user()->hasAnyRole(['Admin', 'Sales Manager', 'Recon Manager']))
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
                                                        <a href="{{ route('sales-assignments.create', $vehicle) }}" class="text-green-600 hover:text-green-900" title="Assign to Sales Team">
                                                            <x-heroicon-o-user-plus class="h-5 w-5" />
                                                        </a>
                                                    @endif
                                                @endif
                                                
                                                <a href="{{ route('vehicles.edit', $vehicle) }}" class="text-yellow-600 hover:text-yellow-900">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </a>
                                                <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this vehicle?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No vehicles found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $vehicles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 