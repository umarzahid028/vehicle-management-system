<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Transport') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('transports.show', $transport) }}">
                    <x-shadcn.button variant="outline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        {{ __('View Details') }}
                    </x-shadcn.button>
                </a>
                <a href="{{ route('transports.index') }}">
                    <x-shadcn.button variant="outline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('Back to List') }}
                    </x-shadcn.button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-200 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Batch Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <span class="block text-sm font-medium text-gray-500">Batch ID</span>
                            <span class="block mt-1 font-semibold">{{ $transport->batch_id }}</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-500">Batch Name</span>
                            <span class="block mt-1">{{ $transport->batch_name ?: 'Not specified' }}</span>
                        </div>
                        <div>
                            <span class="block text-sm font-medium text-gray-500">View Batch</span>
                            <a href="{{ route('transports.batch', ['batchId' => $transport->batch_id]) }}" class="inline-block mt-1 text-indigo-600 hover:text-indigo-900">
                                View all vehicles in this batch
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('transports.update', $transport) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Transport Information -->
                            <div class="bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Transport Information</h3>
                                
                                <!-- Vehicle -->
                                <div class="mb-4">
                                    <label for="vehicle_id" class="block text-sm font-medium text-gray-700">Current Vehicle *</label>
                                    <x-shadcn.select name="vehicle_id" id="vehicle_id" required>
                                        <option value="">-- Select Vehicle --</option>
                                        @foreach ($vehicles as $vehicle)
                                            <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $transport->vehicle_id) == $vehicle->id ? 'selected' : '' }}>
                                                {{ $vehicle->stock_number }} - {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                            </option>
                                        @endforeach
                                    </x-shadcn.select>
                                </div>
                                
                                <!-- Vehicle Batch Management -->
                                <div class="mb-4 border-t border-gray-200 pt-4 mt-4">
                                    <h4 class="font-medium text-gray-700 mb-3">Manage Vehicles in Batch</h4>
                                    
                                    <div class="mb-4">
                                        <label class="flex items-center">
                                            <input type="checkbox" id="toggleVehicleManagement" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 mr-2">
                                            <span class="text-sm font-medium text-gray-700">Add/Remove vehicles in this batch</span>
                                        </label>
                                    </div>
                                    
                                    <div id="vehicleManagementSection" class="hidden">
                                        <div class="mb-4">
                                            <label for="additional_vehicles" class="block text-sm font-medium text-gray-700 mb-2">Add vehicles to batch {{ $transport->batch_id }}</label>
                                            <select id="additional_vehicles" name="additional_vehicle_ids[]" multiple class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" size="5">
                                                @foreach ($vehicles->where('id', '!=', $transport->vehicle_id) as $vehicle)
                                                    @if(!$vehicle->transport_status || $vehicle->transport_status !== 'in_transit')
                                                        <option value="{{ $vehicle->id }}">
                                                            {{ $vehicle->stock_number }} - {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                                        </option>
                                                    @endif
                                                @endforeach
                                            </select>
                                            <p class="mt-1 text-xs text-gray-500">Hold Ctrl/Cmd to select multiple vehicles</p>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Current vehicles in batch</label>
                                            
                                            @php
                                                $batchVehicles = \App\Models\Transport::where('batch_id', $transport->batch_id)
                                                    ->with('vehicle')
                                                    ->get();
                                            @endphp
                                            
                                            @if($batchVehicles->count() > 1)
                                                <div class="space-y-2 max-h-60 overflow-y-auto border border-gray-200 rounded-md p-3">
                                                    @foreach($batchVehicles as $batchTransport)
                                                        @if($batchTransport->id != $transport->id)
                                                            <div class="flex items-start">
                                                                <div class="flex items-center h-5">
                                                                    <input id="remove_vehicle_{{ $batchTransport->id }}" name="remove_vehicle_ids[]" type="checkbox" 
                                                                        value="{{ $batchTransport->id }}" class="h-4 w-4 text-indigo-600 rounded">
                                                                </div>
                                                                <div class="ml-3 text-sm">
                                                                    <label for="remove_vehicle_{{ $batchTransport->id }}" class="font-medium text-gray-700">
                                                                        {{ $batchTransport->vehicle->stock_number }} - {{ $batchTransport->vehicle->year }} {{ $batchTransport->vehicle->make }} {{ $batchTransport->vehicle->model }}
                                                                    </label>
                                                                    <p class="text-gray-500">VIN: {{ $batchTransport->vehicle->vin }}</p>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                                <p class="mt-1 text-xs text-gray-500">Check vehicles to remove from this batch</p>
                                            @else
                                                <p class="text-gray-500">No other vehicles in this batch</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Origin -->
                                <div class="mb-4">
                                    <label for="origin" class="block text-sm font-medium text-gray-700">Origin</label>
                                    <x-shadcn.input
                                        type="text"
                                        name="origin"
                                        id="origin"
                                        :value="old('origin', $transport->origin)"
                                    />
                                </div>
                                
                                <!-- Destination -->
                                <div class="mb-4">
                                    <label for="destination" class="block text-sm font-medium text-gray-700">Destination *</label>
                                    <x-shadcn.input
                                        type="text"
                                        name="destination"
                                        id="destination"
                                        :value="old('destination', $transport->destination)"
                                        required
                                    />
                                </div>
                                
                                <!-- Pickup Date -->
                                <div class="mb-4">
                                    <label for="pickup_date" class="block text-sm font-medium text-gray-700">Pickup Date</label>
                                    <x-shadcn.input
                                        type="date"
                                        name="pickup_date"
                                        id="pickup_date"
                                        :value="old('pickup_date', $transport->pickup_date ? $transport->pickup_date->format('Y-m-d') : '')"
                                    />
                                </div>
                                
                                <!-- Delivery Date -->
                                <div class="mb-4">
                                    <label for="delivery_date" class="block text-sm font-medium text-gray-700">Delivery Date</label>
                                    <x-shadcn.input
                                        type="date"
                                        name="delivery_date"
                                        id="delivery_date"
                                        :value="old('delivery_date', $transport->delivery_date ? $transport->delivery_date->format('Y-m-d') : '')"
                                    />
                                </div>
                                
                                <!-- Gate Pass -->
                                <div class="mb-4 border-t border-gray-200 pt-4 mt-4">
                                    <h4 class="font-medium text-gray-700 mb-3">Gate Pass</h4>
                                    
                                    @if($transport->hasGatePass())
                                        <div class="mb-4">
                                            <span class="block text-sm font-medium text-gray-500 mb-2">Current Gate Pass</span>
                                            <div class="flex items-center">
                                                <a href="{{ $transport->getGatePassUrl() }}" target="_blank" class="inline-flex items-center px-3 py-1.5 text-sm bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    View Gate Pass
                                                </a>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    <label for="gate_pass" class="block text-sm font-medium text-gray-700 mb-2">
                                        {{ $transport->hasGatePass() ? 'Replace Gate Pass' : 'Upload Gate Pass' }}
                                    </label>
                                    <input type="file" id="gate_pass" name="gate_pass" class="block w-full text-sm text-gray-500
                                        file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0
                                        file:text-sm file:font-semibold file:bg-indigo-50
                                        file:text-indigo-700 hover:file:bg-indigo-100" accept=".pdf,.jpg,.jpeg,.png">
                                    <p class="mt-1 text-xs text-gray-500">Accepted formats: PDF, JPG, JPEG, PNG (Max: 10MB)</p>
                                </div>
                            </div>
                            
                            <!-- Transporter Details & Status -->
                            <div class="bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Transporter Details & Status</h3>
                                
                                <!-- Status -->
                                <div class="mb-4">
                                    <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                                    <x-shadcn.select name="status" id="status" required>
                                        <option value="pending" {{ old('status', $transport->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="in_transit" {{ old('status', $transport->status) == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                        <option value="delivered" {{ old('status', $transport->status) == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                        <option value="cancelled" {{ old('status', $transport->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </x-shadcn.select>
                                </div>

                                <!-- QR Code Section -->
                                <div class="mb-4">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-sm font-medium text-gray-700">QR Code for Batch Tracking</label>
                                        @if($transport->qr_code_path)
                                            <a href="{{ Storage::url($transport->qr_code_path) }}" 
                                               target="_blank"
                                               class="text-sm text-blue-600 hover:text-blue-800">
                                                View Current QR Code
                                            </a>
                                        @endif
                                    </div>
                                    <div class="mt-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" 
                                                   name="generate_qr" 
                                                   value="1" 
                                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                   {{ old('generate_qr') ? 'checked' : '' }}>
                                            <span class="ml-2 text-sm text-gray-600">Generate new QR code for batch tracking</span>
                                        </label>
                                    </div>
                                    @if($transport->qr_code_path)
                                        <p class="mt-1 text-xs text-gray-500">
                                            Generating a new QR code will replace the existing one
                                        </p>
                                    @endif
                                </div>
                                
                                <!-- Transporter Selection -->
                                <div class="mb-4">
                                    <label for="transporter_id" class="block text-sm font-medium text-gray-700">Select Transporter</label>
                                    <x-shadcn.select name="transporter_id" id="transporter_id">
                                        <option value="">-- Select a Transporter --</option>
                                        @foreach ($transporters as $transporter)
                                            <option value="{{ $transporter->id }}" {{ old('transporter_id', $transport->transporter_id) == $transporter->id ? 'selected' : '' }}>
                                                {{ $transporter->full_name }}
                                            </option>
                                        @endforeach
                                    </x-shadcn.select>
                                    <p class="mt-1 text-xs text-gray-500">Select a transporter or enter details manually below</p>
                                </div>
                                
                                <div class="border-t border-gray-200 pt-4 mt-4">
                                    <h4 class="font-medium text-gray-700 mb-3">Or Enter Transporter Details Manually</h4>
                                
                                    <!-- Transporter Name -->
                                    <div class="mb-4">
                                        <label for="transporter_name" class="block text-sm font-medium text-gray-700">Transporter Name</label>
                                        <x-shadcn.input
                                            type="text"
                                            name="transporter_name"
                                            id="transporter_name"
                                            :value="old('transporter_name', $transport->transporter_name)"
                                        />
                                    </div>
                                    
                                    <!-- Transporter Phone -->
                                    <div class="mb-4">
                                        <label for="transporter_phone" class="block text-sm font-medium text-gray-700">Transporter Phone</label>
                                        <x-shadcn.input
                                            type="text"
                                            name="transporter_phone"
                                            id="transporter_phone"
                                            :value="old('transporter_phone', $transport->transporter_phone)"
                                        />
                                    </div>
                                    
                                    <!-- Transporter Email -->
                                    <div class="mb-4">
                                        <label for="transporter_email" class="block text-sm font-medium text-gray-700">Transporter Email</label>
                                        <x-shadcn.input
                                            type="email"
                                            name="transporter_email"
                                            id="transporter_email"
                                            :value="old('transporter_email', $transport->transporter_email)"
                                        />
                                    </div>
                                </div>
                                
                                <!-- Notes -->
                                <div class="mb-4">
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                    <textarea name="notes" id="notes" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('notes', $transport->notes) }}</textarea>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="mt-6 flex justify-end">
                                    <x-shadcn.button type="submit" variant="default">
                                        Update Transport
                                    </x-shadcn.button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const transporterId = document.getElementById('transporter_id');
            const transporterName = document.getElementById('transporter_name');
            const transporterPhone = document.getElementById('transporter_phone');
            const transporterEmail = document.getElementById('transporter_email');
            
            // Function to toggle manual transporter fields
            function toggleManualTransporterFields() {
                const isTransporterSelected = transporterId.value !== '';
                const fieldsToToggle = [transporterName, transporterPhone, transporterEmail];
                
                fieldsToToggle.forEach(field => {
                    field.disabled = isTransporterSelected;
                    if (isTransporterSelected) {
                        field.classList.add('bg-gray-100');
                        field.value = '';
                    } else {
                        field.classList.remove('bg-gray-100');
                    }
                });
            }
            
            // Initialize on page load
            toggleManualTransporterFields();
            
            // Add event listener for changes
            transporterId.addEventListener('change', toggleManualTransporterFields);
            
            // Add vehicle management toggle
            const toggleVehicleManagement = document.getElementById('toggleVehicleManagement');
            const vehicleManagementSection = document.getElementById('vehicleManagementSection');
            
            toggleVehicleManagement.addEventListener('change', function() {
                if (this.checked) {
                    vehicleManagementSection.classList.remove('hidden');
                } else {
                    vehicleManagementSection.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout> 