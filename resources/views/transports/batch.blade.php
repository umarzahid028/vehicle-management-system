<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-tight">Batch Transport</h2>
                <p class="text-sm text-muted-foreground">Create multiple transport requests at once.</p>
            </div>
        </div>
    </x-slot>

    <div class="container mx-auto space-y-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Batch Information Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Batch Status</h3>
                            <p class="mt-1 text-md font-semibold">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $batchData->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $batchData->status === 'in_transit' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $batchData->status === 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $batchData->status === 'cancelled' ? 'bg-red-100 text-red-800' : '' }}
                                ">
                                    {{ ucfirst($batchData->status) }}
                                </span>
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Origin</h3>
                            <p class="mt-1 text-md font-semibold">{{ $batchData->origin ?: 'Not specified' }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Destination</h3>
                            <p class="mt-1 text-md font-semibold">{{ $batchData->destination }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Vehicle Count</h3>
                            <p class="mt-1 text-md font-semibold">{{ $transports->count() }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Pickup Date</h3>
                            <p class="mt-1 text-md font-semibold">{{ $batchData->pickup_date ? date('M d, Y', strtotime($batchData->pickup_date)) : 'Not scheduled' }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Delivery Date</h3>
                            <p class="mt-1 text-md font-semibold">{{ $batchData->delivery_date ? date('M d, Y', strtotime($batchData->delivery_date)) : 'Not scheduled' }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Transporter</h3>
                            <p class="mt-1 text-md font-semibold">
                                @if($batchData->transporter_id)
                                    {{ optional($batchData->transporter)->full_name }}
                                @elseif($batchData->transporter_name)
                                    {{ $batchData->transporter_name }}
                                @else
                                    Not assigned
                                @endif
                            </p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Created On</h3>
                            <p class="mt-1 text-md font-semibold">{{ date('M d, Y', strtotime($batchData->created_at)) }}</p>
                        </div>
                    </div>
                    
                    @if($batchData->qr_code_path)
                        <div class="mt-6 flex justify-center">
                            <div class="text-center">
                                <h3 class="text-sm font-medium text-gray-500 mb-2">Batch QR Code</h3>
                                <div class="bg-white p-4 inline-block rounded-lg shadow-sm border border-gray-200">
                                    <img src="{{ Storage::url($batchData->qr_code_path) }}" alt="Batch QR Code" class="h-48 w-48 mx-auto">
                                    <p class="mt-2 text-xs text-gray-500">Scan to track this batch</p>
                                    <p class="text-xs text-gray-700 truncate mt-1">{{ url("/track/{$batchId}") }}</p>
                                    <a href="{{ Storage::url($batchData->qr_code_path) }}" download="batch-{{ $batchId }}-qr.png" class="mt-3 inline-flex items-center px-3 py-2 text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                        </svg>
                                        Download QR Code
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($batchData->notes)
                        <div class="mt-6 border-t border-gray-200 pt-4">
                            <h3 class="text-sm font-medium text-gray-500 mb-2">Notes</h3>
                            <p class="text-gray-700 whitespace-pre-line">{{ $batchData->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Vehicles in Batch -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Vehicles in Batch</h3>
                    
                    @if(auth()->user()->hasRole('Transporter'))
                        <form action="{{ route('transports.batch.update-status', $batchId) }}" method="POST" class="mb-6">
                            @csrf
                            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700">Update Status</label>
                                        <select name="status" id="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                            <option value="">Select Status</option>
                                            @if($batchData->status === 'pending' || $batchData->status === 'in_transit')
                                                <option value="picked_up">Mark Selected as Picked Up</option>
                                            @endif
                                            @if($batchData->status === 'in_transit' || $batchData->status === 'picked_up')
                                                <option value="delivered">Mark Selected as Delivered</option>
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
                                </div>
                                
                                <div class="mt-4 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                        <label for="selectAll" class="ml-2 text-sm text-gray-700">Select All Vehicles</label>
                                    </div>
                                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Update Selected Vehicles
                                    </button>
                                </div>
                            </div>
                    @endif
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    @if(auth()->user()->hasRole('Transporter'))
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Select
                                        </th>
                                    @endif
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Vehicle
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stock Number
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        VIN
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Gate Pass
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($transports as $transport)
                                    <tr>
                                        @if(auth()->user()->hasRole('Transporter'))
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($transport->status === 'pending' || $transport->status === 'in_transit' || $transport->status === 'picked_up')
                                                    <input type="checkbox" name="transport_ids[]" value="{{ $transport->id }}" 
                                                        class="transport-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                        @if($transport->status === 'delivered') disabled @endif>
                                                @endif
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $transport->vehicle->year }} {{ $transport->vehicle->make }} {{ $transport->vehicle->model }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $transport->vehicle->color }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transport->vehicle->stock_number }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $transport->vehicle->vin }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                {{ $transport->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                {{ $transport->status == 'in_transit' ? 'bg-blue-100 text-blue-800' : '' }}
                                                {{ $transport->status == 'delivered' ? 'bg-green-100 text-green-800' : '' }}
                                                {{ $transport->status == 'cancelled' ? 'bg-red-100 text-red-800' : '' }}">
                                                {{ ucfirst($transport->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            @if($transport->hasGatePass())
                                                <a href="{{ $transport->getGatePassUrl() }}" target="_blank" class="text-indigo-600 hover:text-indigo-900">View</a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <a href="{{ route('transports.show', $transport) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                View
                                            </a>
                                            @if(!auth()->user()->hasRole('Transporter'))
                                                <a href="{{ route('transports.edit', $transport) }}" class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                    Edit
                                                </a>
                                                @if(auth()->user()->hasAnyRole(['Admin', 'Manager']))
                                                    <form action="{{ route('transports.destroy', $transport) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this transport? This action cannot be undone.')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if(auth()->user()->hasRole('Transporter'))
                        </form>
                        
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const statusSelect = document.getElementById('status');
                                const pickupDateField = document.getElementById('pickupDateField');
                                const deliveryDateField = document.getElementById('deliveryDateField');
                                const selectAllCheckbox = document.getElementById('selectAll');
                                const transportCheckboxes = document.querySelectorAll('.transport-checkbox');
                                
                                // Handle status change
                                statusSelect.addEventListener('change', function() {
                                    pickupDateField.classList.add('hidden');
                                    deliveryDateField.classList.add('hidden');
                                    
                                    if (this.value === 'picked_up') {
                                        pickupDateField.classList.remove('hidden');
                                        document.getElementById('pickup_date').required = true;
                                        document.getElementById('delivery_date').required = false;
                                    } else if (this.value === 'delivered') {
                                        deliveryDateField.classList.remove('hidden');
                                        document.getElementById('pickup_date').required = false;
                                        document.getElementById('delivery_date').required = true;
                                    }
                                });
                                
                                // Handle select all checkbox
                                selectAllCheckbox.addEventListener('change', function() {
                                    transportCheckboxes.forEach(checkbox => {
                                        if (!checkbox.disabled) {
                                            checkbox.checked = this.checked;
                                        }
                                    });
                                });
                                
                                // Update select all state when individual checkboxes change
                                transportCheckboxes.forEach(checkbox => {
                                    checkbox.addEventListener('change', function() {
                                        const allChecked = Array.from(transportCheckboxes)
                                            .filter(cb => !cb.disabled)
                                            .every(cb => cb.checked);
                                        selectAllCheckbox.checked = allChecked;
                                    });
                                });

                                // Form validation
                                const form = document.querySelector('form');
                                form.addEventListener('submit', function(e) {
                                    const checkedTransports = document.querySelectorAll('input[name="transport_ids[]"]:checked');
                                    if (checkedTransports.length === 0) {
                                        e.preventDefault();
                                        alert('Please select at least one vehicle to update.');
                                        return;
                                    }

                                    const status = statusSelect.value;
                                    if (!status) {
                                        e.preventDefault();
                                        alert('Please select a status.');
                                        return;
                                    }

                                    if (status === 'in_transit' && !document.getElementById('pickup_date').value) {
                                        e.preventDefault();
                                        alert('Please select a pickup date.');
                                        return;
                                    }

                                    if (status === 'delivered' && !document.getElementById('delivery_date').value) {
                                        e.preventDefault();
                                        alert('Please select a delivery date.');
                                        return;
                                    }
                                });
                            });
                        </script>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 