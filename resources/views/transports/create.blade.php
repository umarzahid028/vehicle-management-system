<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-tight">Create Transport</h2>
                <p class="text-sm text-muted-foreground">Create a new transport request for vehicles.</p>
            </div>
            <div>
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

    <div class="container mx-auto space-y-6">
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
                
                <form action="{{ route('transports.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Transport Information -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Transport Information</h3>
                            
                            <!-- Batch Details -->
                            <div class="mb-4">
                                <div class="flex items-center mb-2">
                                    <label class="block text-sm font-medium text-gray-700">Batch ID</label>
                                    <span class="ml-2 px-2 py-1 bg-gray-100 text-xs font-medium rounded">Auto-generated</span>
                                </div>
                                <div class="mb-4">
                                    <label for="batch_name" class="block text-sm font-medium text-gray-700">Batch Name (Optional)</label>
                                    <x-shadcn.input
                                        type="text"
                                        name="batch_name"
                                        id="batch_name"
                                        :value="old('batch_name')"
                                    />
                                </div>
                            </div>
                            
                            <!-- Origin -->
                            <div class="mb-4">
                                <label for="origin" class="block text-sm font-medium text-gray-700">Origin</label>
                                <x-shadcn.input
                                    type="text"
                                    name="origin"
                                    id="origin"
                                    :value="old('origin')"
                                />
                            </div>
                            
                            <!-- Destination -->
                            <div class="mb-4">
                                <label for="destination" class="block text-sm font-medium text-gray-700">Destination *</label>
                                <x-shadcn.input
                                    type="text"
                                    name="destination"
                                    id="destination"
                                    :value="old('destination')"
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
                                    :value="old('pickup_date')"
                                />
                            </div>
                            
                            <!-- Delivery Date -->
                            <div class="mb-4">
                                <label for="delivery_date" class="block text-sm font-medium text-gray-700">Delivery Date</label>
                                <x-shadcn.input
                                    type="date"
                                    name="delivery_date"
                                    id="delivery_date"
                                    :value="old('delivery_date')"
                                />
                            </div>
                        </div>
                        
                        <!-- Transporter Details & Status -->
                        <div class="bg-white p-6 rounded-lg shadow-md">
                            <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Transporter Details & Status</h3>
                            
                            <!-- Status -->
                            <div class="mb-4">
                                <label for="status" class="block text-sm font-medium text-gray-700">Status *</label>
                                <x-shadcn.select name="status" id="status" required>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="in_transit" {{ old('status') == 'in_transit' ? 'selected' : '' }}>In Transit</option>
                                    <option value="delivered" {{ old('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                    <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </x-shadcn.select>
                            </div>
                            
                            <!-- Transporter Selection -->
                            <div class="mb-4">
                                <label for="transporter_id" class="block text-sm font-medium text-gray-700">Select Transporter</label>
                                <x-shadcn.select name="transporter_id" id="transporter_id">
                                    <option value="">-- Select a Transporter --</option>
                                    @foreach ($transporters as $transporter)
                                        <option value="{{ $transporter->id }}" {{ old('transporter_id') == $transporter->id ? 'selected' : '' }}>
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
                                        :value="old('transporter_name')"
                                    />
                                </div>
                                
                                <!-- Transporter Phone -->
                                <div class="mb-4">
                                    <label for="transporter_phone" class="block text-sm font-medium text-gray-700">Transporter Phone</label>
                                    <x-shadcn.input
                                        type="text"
                                        name="transporter_phone"
                                        id="transporter_phone"
                                        :value="old('transporter_phone')"
                                    />
                                </div>
                                
                                <!-- Transporter Email -->
                                <div class="mb-4">
                                    <label for="transporter_email" class="block text-sm font-medium text-gray-700">Transporter Email</label>
                                    <x-shadcn.input
                                        type="email"
                                        name="transporter_email"
                                        id="transporter_email"
                                        :value="old('transporter_email')"
                                    />
                                </div>
                            </div>
                            
                            <!-- Notes -->
                            <div class="mb-4">
                                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                <textarea name="notes" id="notes" rows="4" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Vehicle Selection & Gate Pass Upload -->
                    <div class="mt-6 bg-white p-6 rounded-lg shadow-md">
                        <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Vehicle Selection & Gate Passes</h3>
                        
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <label class="block text-sm font-medium text-gray-700">Select Vehicles</label>
                                <div class="flex gap-2">
                                    <button type="button" id="selectAllBtn" class="text-sm text-indigo-600 hover:text-indigo-900">
                                        Select All
                                    </button>
                                    <button type="button" id="deselectAllBtn" class="text-sm text-gray-600 hover:text-gray-900">
                                        Deselect All
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mb-2">
                                <x-shadcn.input 
                                    type="text" 
                                    id="vehicleSearch" 
                                    placeholder="Search vehicles..." 
                                    class="w-full"
                                />
                            </div>
                            
                            <div class="space-y-4 max-h-96 overflow-y-auto border border-gray-200 rounded-md p-4">
                                @if($vehicles->isEmpty())
                                    <p class="text-gray-500 text-center py-4">No available vehicles found.</p>
                                @else
                                    @foreach($vehicles as $index => $vehicle)
                                        <div class="vehicle-item border border-gray-200 rounded-md p-4 {{ $index > 0 ? 'mt-4' : '' }}">
                                            <div class="flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input id="vehicle_{{ $vehicle->id }}" name="vehicle_ids[]" type="checkbox" 
                                                        value="{{ $vehicle->id }}" class="vehicle-checkbox h-4 w-4 text-indigo-600 rounded"
                                                        {{ in_array($vehicle->id, old('vehicle_ids', [])) ? 'checked' : '' }}>
                                                </div>
                                                <div class="ml-3 flex-grow">
                                                    <label for="vehicle_{{ $vehicle->id }}" class="font-medium text-gray-700 cursor-pointer">
                                                        {{ $vehicle->stock_number }} - {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                                    </label>
                                                    <p class="text-sm text-gray-500">VIN: {{ $vehicle->vin }}</p>
                                                </div>
                                            </div>
                                            
                                            <!-- Gate Pass Upload -->
                                            <div class="mt-3 ml-7 gate-pass-upload hidden">
                                                <label for="gate_pass_{{ $vehicle->id }}" class="block text-sm font-medium text-gray-700">
                                                    Upload Gate Pass
                                                </label>
                                                <input type="file" id="gate_pass_{{ $vehicle->id }}" 
                                                    name="gate_passes[{{ $vehicle->id }}]"
                                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4
                                                    file:rounded-md file:border-0 file:text-sm file:font-semibold
                                                    file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                                    accept=".pdf,.jpg,.jpeg,.png">
                                                <p class="mt-1 text-xs text-gray-500">Accepted formats: PDF, JPG, JPEG, PNG (Max: 10MB)</p>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        
                        <!-- QR Code Tracking -->
                        <div class="mt-6 mb-4">
                            <div class="flex items-center">
                                <input type="checkbox" id="generate_qr" name="generate_qr" value="1" class="h-4 w-4 text-indigo-600 rounded"
                                    {{ old('generate_qr') ? 'checked' : '' }}>
                                <label for="generate_qr" class="ml-2 block text-sm font-medium text-gray-700">
                                    Generate QR Code for batch tracking
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500 ml-6">
                                A QR code will be generated that links to the batch details page for easy tracking
                            </p>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="mt-6 flex justify-end">
                        <x-shadcn.button type="submit" variant="default">
                            Create Transport
                        </x-shadcn.button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle vehicle checkboxes and gate pass upload visibility
            const vehicleCheckboxes = document.querySelectorAll('.vehicle-checkbox');
            vehicleCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const gatePassUpload = this.closest('.vehicle-item').querySelector('.gate-pass-upload');
                    if (this.checked) {
                        gatePassUpload.classList.remove('hidden');
                    } else {
                        gatePassUpload.classList.add('hidden');
                    }
                });
                
                // Initialize visibility based on checked state
                if (checkbox.checked) {
                    checkbox.closest('.vehicle-item').querySelector('.gate-pass-upload').classList.remove('hidden');
                }
            });
            
            // Select All / Deselect All buttons
            document.getElementById('selectAllBtn').addEventListener('click', function() {
                vehicleCheckboxes.forEach(checkbox => {
                    checkbox.checked = true;
                    checkbox.closest('.vehicle-item').querySelector('.gate-pass-upload').classList.remove('hidden');
                });
            });
            
            document.getElementById('deselectAllBtn').addEventListener('click', function() {
                vehicleCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                    checkbox.closest('.vehicle-item').querySelector('.gate-pass-upload').classList.add('hidden');
                });
            });
            
            // Vehicle search functionality
            document.getElementById('vehicleSearch').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                document.querySelectorAll('.vehicle-item').forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(searchTerm)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    </script>
</x-app-layout> 