<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-zinc-800 dark:text-zinc-200">
                {{ __('Create Batch Transport') }}
            </h2>
        </div>
    </x-slot>

    <div class="container mx-auto px-4 py-8">
        <div class="mb-6">
            <p class="text-gray-600">Create multiple transports at once</p>
        </div>

        <form action="{{ route('transports.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- Vehicles Selection -->
            <div class="space-y-2">
                <label for="vehicle_ids" class="block text-sm font-medium text-gray-700">Select Vehicles</label>
                <select name="vehicle_ids[]" id="vehicle_ids" multiple class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md" required>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->id }}">
                            {{ $vehicle->stock_number }} - {{ $vehicle->make }} {{ $vehicle->model }} ({{ $vehicle->vin }})
                        </option>
                    @endforeach
                </select>
                @error('vehicle_ids')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Transporter Selection -->
            <div class="space-y-2">
                <label for="transporter_id" class="block text-sm font-medium text-gray-700">Select Transporter (Optional)</label>
                <select name="transporter_id" id="transporter_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">Select a transporter</option>
                    @foreach($transporters as $transporter)
                        <option value="{{ $transporter->id }}">{{ $transporter->name }}</option>
                    @endforeach
                </select>
                @error('transporter_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Manual Transporter Details -->
            <div id="manual_transporter_details" class="space-y-4">
                <div>
                    <label for="transporter_name" class="block text-sm font-medium text-gray-700">Transporter Name</label>
                    <input type="text" name="transporter_name" id="transporter_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('transporter_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="transporter_phone" class="block text-sm font-medium text-gray-700">Transporter Phone</label>
                    <input type="text" name="transporter_phone" id="transporter_phone" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('transporter_phone')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="transporter_email" class="block text-sm font-medium text-gray-700">Transporter Email</label>
                    <input type="email" name="transporter_email" id="transporter_email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('transporter_email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Transport Details -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="origin" class="block text-sm font-medium text-gray-700">Origin</label>
                    <input type="text" name="origin" id="origin" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('origin')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="destination" class="block text-sm font-medium text-gray-700">Destination</label>
                    <input type="text" name="destination" id="destination" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('destination')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="pickup_date" class="block text-sm font-medium text-gray-700">Pickup Date</label>
                    <input type="date" name="pickup_date" id="pickup_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('pickup_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="delivery_date" class="block text-sm font-medium text-gray-700">Delivery Date</label>
                    <input type="date" name="delivery_date" id="delivery_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    @error('delivery_date')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="batch_name" class="block text-sm font-medium text-gray-700">Batch Name (Optional)</label>
                <input type="text" name="batch_name" id="batch_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                @error('batch_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                <textarea name="notes" id="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                @error('notes')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                <select name="status" id="status" required class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="pending">Pending</option>
                    <option value="in_transit">In Transit</option>
                    <option value="delivered">Delivered</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="generate_qr" id="generate_qr" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="generate_qr" class="ml-2 block text-sm text-gray-900">Generate QR Code for Tracking</label>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('transports.index') }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-gray-700 bg-gray-100 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                    Cancel
                </a>
                <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Create Batch Transport
                </button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const transporterSelect = document.getElementById('transporter_id');
            const manualTransporterDetails = document.getElementById('manual_transporter_details');
            const manualInputs = manualTransporterDetails.querySelectorAll('input');

            function toggleManualTransporterFields() {
                const isManualEntry = !transporterSelect.value;
                manualTransporterDetails.style.display = isManualEntry ? 'block' : 'none';
                manualInputs.forEach(input => {
                    if (!isManualEntry) {
                        input.value = '';
                    }
                });
            }

            transporterSelect.addEventListener('change', toggleManualTransporterFields);
            toggleManualTransporterFields();
        });
    </script>
    @endpush
</x-app-layout> 