<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Create New Sale') }}
        </h2>
    </x-slot>

    

    <div class="py-12">
        <div class="mx-auto max-w-full sm:px-6 lg:px-8">
            @include('layouts.partials.messages')
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('sales-team.sales.store') }}">
                        @csrf
                        
                        <!-- Vehicle Selection -->
                        <div class="mb-6">
                            <x-input-label for="vehicle_id" :value="__('Select Vehicle')" />
                            <select id="vehicle_id" name="vehicle_id" required
                                class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Select a Vehicle --</option>
                                @foreach($availableVehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ (old('vehicle_id', $selectedVehicleId) == $vehicle->id) ? 'selected' : '' }}>
                                        {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }} - ${{ number_format($vehicle->price, 2) }} (VIN: {{ $vehicle->vin }})
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('vehicle_id')" class="mt-2" />
                        </div>
                        
                        <!-- Customer Information -->
                        <div class="mb-6">
                            <x-input-label for="customer_name" :value="__('Customer Name')" />
                            <x-text-input id="customer_name" name="customer_name" type="text" class="block w-full mt-1" 
                                value="{{ old('customer_name') }}" required />
                            <x-input-error :messages="$errors->get('customer_name')" class="mt-2" />
                        </div>
                        
                        <div class="mb-6">
                            <x-input-label for="customer_email" :value="__('Customer Email')" />
                            <x-text-input id="customer_email" name="customer_email" type="email" class="block w-full mt-1" 
                                value="{{ old('customer_email') }}" required />
                            <x-input-error :messages="$errors->get('customer_email')" class="mt-2" />
                        </div>
                        
                        <div class="mb-6">
                            <x-input-label for="customer_phone" :value="__('Customer Phone')" />
                            <x-text-input id="customer_phone" name="customer_phone" type="text" class="block w-full mt-1" 
                                value="{{ old('customer_phone') }}" required />
                            <x-input-error :messages="$errors->get('customer_phone')" class="mt-2" />
                        </div>
                        
                        <div class="mb-6">
                            <x-input-label for="sale_amount" :value="__('Sale Amount')" />
                            <x-text-input id="sale_amount" name="amount" type="number" step="0.01" class="block w-full mt-1" 
                                value="{{ old('sale_amount') }}" required />
                            <x-input-error :messages="$errors->get('sale_amount')" class="mt-2" />
                        </div>
                        
                        <div class="flex items-center justify-end mt-4">
                            <x-primary-button class="ml-4">
                                {{ __('Create Sale') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 