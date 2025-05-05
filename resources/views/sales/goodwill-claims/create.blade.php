<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-gray-800 dark:text-gray-200 leading-tight text-center w-full">
                {{ __('GOODWILL REPAIR CLAIM') }}
            </h2>
        </div>
    </x-slot>

    <div class="container mx-auto space-y-6 py-6">
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
            <div class="p-8">
                <form action="{{ route('sales.goodwill-claims.store') }}" method="POST" class="space-y-8">
                    @csrf

                    <!-- Dealership Information -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="dealership_name" :value="__('Dealership')" class="text-base" />
                            <x-text-input id="dealership_name" type="text" 
                                class="mt-1 block w-full rounded-lg bg-gray-100" 
                                value="Trevino's Auto Mart" 
                                readonly />
                        </div>
                        <div>
                            <x-input-label for="created_by" :value="__('Created By')" class="text-base" />
                            <x-text-input id="created_by" type="text" 
                                class="mt-1 block w-full rounded-lg bg-gray-100" 
                                value="{{ Auth::user()->name }}" 
                                readonly />
                        </div>
                    </div>

                    <!-- Customer Information -->
                    <div class="space-y-6">
                        <div>
                            <x-input-label for="customer_name" :value="__('Customer Name(s)')" class="text-base" />
                            <x-text-input id="customer_name" name="customer_name" type="text" 
                                class="mt-1 block w-full rounded-lg" 
                                :value="old('customer_name')" 
                                required />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="customer_phone" :value="__('Customer Phone')" class="text-base" />
                                <x-text-input id="customer_phone" name="customer_phone" type="tel" 
                                    class="mt-1 block w-full rounded-lg" 
                                    :value="old('customer_phone')" 
                                    placeholder="(555) 555-5555" 
                                    required />
                            </div>
                            <div>
                                <x-input-label for="customer_email" :value="__('Customer Email')" class="text-base" />
                                <x-text-input id="customer_email" name="customer_email" type="email" 
                                    class="mt-1 block w-full rounded-lg" 
                                    :value="old('customer_email')" 
                                    placeholder="customer@example.com" />
                            </div>
                        </div>
                    </div>

                    <!-- Vehicle Information -->
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="vehicle_id" :value="__('Vehicle')" class="text-base" />
                                <select id="vehicle_id" name="vehicle_id" 
                                    class="mt-1 block w-full pl-3 pr-10 py-2.5 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 rounded-lg shadow-sm bg-white dark:bg-gray-700 dark:text-gray-300" 
                                    required>
                                    <option value="">Select Vehicle</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $selectedVehicle?->id) == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->stock_number }} - {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <x-input-label for="sales_issue_id" :value="__('Related Sales Issue')" class="text-base" />
                                <select id="sales_issue_id" name="sales_issue_id" 
                                    class="mt-1 block w-full pl-3 pr-10 py-2.5 text-base border-gray-300 dark:border-gray-600 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-primary-500 rounded-lg shadow-sm bg-white dark:bg-gray-700 dark:text-gray-300">
                                    <option value="">None</option>
                                    @if($salesIssue)
                                        <option value="{{ $salesIssue->id }}" selected>
                                            Issue #{{ $salesIssue->id }} - ({{ ucfirst($salesIssue->issue_type) }}) - {{ Str::limit($salesIssue->description, 40) }}
                                        </option>
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Repair Details -->
                    <div class="space-y-6">
                        <div>
                            <x-input-label for="issue_description" :value="__('Issue Description')" class="text-base" />
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                Describe the problem that needs to be addressed:
                            </p>
                            <textarea id="issue_description" name="issue_description" rows="4" 
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-gray-300" 
                                required>{{ old('issue_description') }}</textarea>
                        </div>
                        
                        <div>
                            <x-input-label for="requested_resolution" :value="__('Requested Resolution')" class="text-base" />
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                                Describe what needs to be done to resolve the issue:
                            </p>
                            <textarea id="requested_resolution" name="requested_resolution" rows="4" 
                                class="mt-1 block w-full rounded-lg border-gray-300 dark:border-gray-600 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:bg-gray-700 dark:text-gray-300" 
                                required>{{ old('requested_resolution') }}</textarea>
                        </div>
                        
                        <div>
                            <x-input-label for="estimated_cost" :value="__('Estimated Cost ($)')" class="text-base" />
                            <x-text-input id="estimated_cost" name="estimated_cost" type="number" 
                                class="mt-1 block w-full rounded-lg" 
                                :value="old('estimated_cost')" 
                                step="0.01" 
                                min="0" />
                        </div>
                    </div>

                    <!-- Customer Consent -->
                    <div class="space-y-6">
                        <div class="flex items-start space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div class="flex-shrink-0">
                                <input id="customer_consent" name="customer_consent" type="checkbox" value="1"
                                    class="h-5 w-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700"
                                    {{ old('customer_consent') ? 'checked' : '' }}
                                    required>
                            </div>
                            <div>
                                <label for="customer_consent" class="text-base font-medium text-gray-900 dark:text-gray-100">
                                    I confirm that the customer has consented to this goodwill repair
                                </label>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    By checking this box, you affirm that the customer has been informed and has agreed to the terms of this goodwill repair.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Disclaimers and Agreements -->
                    <div class="space-y-6">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm text-gray-600 dark:text-gray-400">
                            <p class="mb-4">
                                By submitting this form, you acknowledge that the above-listed repairs that you are requesting the Dealership to attempt are not covered under the terms of any warranty and that the Dealership is not obligated to perform them. The Dealership is in no way creating a warranty of any kind on the vehicle by attempting the "goodwill" repairs at no charge to the customer.
                            </p>
                            <p class="mb-4">
                                The dealership disclaims all warranties, express or implied, including any implied warranties of merchantability or fitness for a particular purpose relating to this repair and all goods and services utilized and/or performed in conjunction with this repair.
                            </p>
                            <p>
                                The Dealership will not be liable for any damage to the vehicle or its contents due to fire, theft, an act of nature, or any cause beyond the Dealership's control.
                            </p>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-4 pt-6">
                        <a href="{{ route('sales.goodwill-claims.index') }}" 
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                            {{ __('Cancel') }}
                        </a>
                        <button type="submit" 
                            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2">
                            {{ __('Create Claim') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if (session('error'))
            <div x-data="{ show: true }"
                 x-show="show"
                 x-transition
                 x-init="setTimeout(() => show = false, 3000)"
                 class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
                {{ session('error') }}
            </div>
        @endif
    </div>
</x-app-layout>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Handle vehicle selection
        const vehicleSelect = document.getElementById('vehicle_id');
        
        if (vehicleSelect) {
            vehicleSelect.addEventListener('change', function() {
                // Highlight the selected vehicle for better UX
                if (this.value) {
                    this.classList.add('border-primary-500');
                } else {
                    this.classList.remove('border-primary-500');
                }
            });
        }

        // Initialize form validation
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(event) {
                // Validate required fields
                const requiredFields = form.querySelectorAll('[required]');
                let hasErrors = false;
                
                requiredFields.forEach(field => {
                    if (!field.value && field.type !== 'checkbox') {
                        field.classList.add('border-red-500');
                        hasErrors = true;
                    } else if (field.type === 'checkbox' && !field.checked) {
                        field.parentElement.classList.add('border-red-500');
                        hasErrors = true;
                    } else {
                        field.classList.remove('border-red-500');
                        if (field.type === 'checkbox') {
                            field.parentElement.classList.remove('border-red-500');
                        }
                    }
                });
                
                if (hasErrors) {
                    event.preventDefault();
                    alert('Please fill in all required fields');
                }
            });
        }
    });
</script>
@endpush 