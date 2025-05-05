<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Vendor') }}: {{ $vendor->name }}
            </h2>
            <a href="{{ route('vendors.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                Back to Vendors
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                            <p class="font-bold">Please fix the following errors:</p>
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('vendors.update', $vendor) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <x-input-label for="name" :value="__('Vendor Name')" required />
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-heroicon-o-building-office class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <x-text-input id="name" class="block mt-1 w-full pl-10" type="text" name="name" 
                                        :value="old('name', $vendor->name)" required placeholder="Enter vendor name" />
                                </div>
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Specialty Tags -->
                            <div>
                              
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-heroicon-o-tag class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <x-searchable-multi-select
                                        name="specialty_tags"
                                        label="Specialty Tags"
                                        :options="$specialties"
                                        :selected="old('specialty_tags', $vendor->specialty_tags ?? [])"
                                        placeholder="Select specialties..."
                                        help-text="Select all specialties that apply to this vendor"
                                        required
                                    />
                                </div>
                                <x-input-error :messages="$errors->get('specialty_tags')" class="mt-2" />
                            </div>

                            <!-- Vendor Type -->
                            <div>
                                <x-input-label for="type_id" :value="__('Vendor Type')" />
                                <select id="type_id" name="type_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">Select vendor type</option>
                                    @foreach($types as $type)
                                        <option value="{{ $type->id }}" 
                                            {{ old('type_id', $vendor->type_id) == $type->id ? 'selected' : '' }}
                                            data-is-onsite="{{ $type->is_on_site ? '1' : '0' }}">
                                            {{ $type->name }} ({{ $type->is_on_site ? 'On-Site' : 'Off-Site' }})
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('type_id')" class="mt-2" />
                                <p class="mt-1 text-xs text-gray-500">Determines if vendor is on-site or off-site and how they interact with the system</p>
                            </div>

                            <!-- Contact Person -->
                            <div>
                                <x-input-label for="contact_person" :value="__('Contact Person')" />
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-heroicon-o-user class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <x-text-input id="contact_person" class="block mt-1 w-full pl-10" type="text" name="contact_person" 
                                        :value="old('contact_person', $vendor->contact_person)" placeholder="Primary contact person" />
                                </div>
                                <x-input-error :messages="$errors->get('contact_person')" class="mt-2" />
                            </div>

                            <!-- Phone -->
                            <div>
                                <x-input-label for="phone" :value="__('Phone Number')" />
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-heroicon-o-phone class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <x-text-input id="phone" class="block mt-1 w-full pl-10" type="tel" name="phone" 
                                        :value="old('phone', $vendor->phone)" placeholder="(555) 555-5555" />
                                </div>
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>

                            <!-- Email -->
                            <div>
                                <x-input-label for="email" :value="__('Email')" required />
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-heroicon-o-envelope class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <x-text-input id="email" class="block mt-1 w-full pl-10" type="email" name="email" 
                                        :value="old('email', $vendor->email)" required placeholder="vendor@example.com" />
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Address -->
                            <div>
                                <x-input-label for="address" :value="__('Address')" />
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-heroicon-o-map-pin class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <x-text-input id="address" class="block mt-1 w-full pl-10" type="text" name="address" 
                                        :value="old('address', $vendor->address)" placeholder="Full business address" />
                                </div>
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>

                            <!-- Password -->
                            <div class="password-fields">
                                <x-input-label for="password" :value="__('New Password')" />
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-heroicon-o-key class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <x-text-input id="password" class="block mt-1 w-full pl-10" type="password" name="password" 
                                        placeholder="Leave blank to keep current password" />
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Password Confirmation -->
                            <div class="password-fields">
                                <x-input-label for="password_confirmation" :value="__('Confirm New Password')" />
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <x-heroicon-o-key class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <x-text-input id="password_confirmation" class="block mt-1 w-full pl-10" type="password" 
                                        name="password_confirmation" placeholder="Confirm new password" />
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>

                            <!-- Notes -->
                            <div class="md:col-span-2">
                                <x-input-label for="notes" :value="__('Notes')" />
                                <div class="relative">
                                    <div class="absolute top-3 left-3 pointer-events-none">
                                        <x-heroicon-o-document-text class="h-5 w-5 text-gray-400" />
                                    </div>
                                    <textarea id="notes" name="notes" rows="3" 
                                        class="block mt-1 w-full pl-10 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                        placeholder="Additional notes about the vendor">{{ old('notes', $vendor->notes) }}</textarea>
                                </div>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div class="md:col-span-2">
                                <div class="flex items-center">
                                    <input id="is_active" name="is_active" type="checkbox" value="1" 
                                        {{ old('is_active', $vendor->is_active) ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                        Vendor is active and available for new inspections
                                    </label>
                                </div>
                                <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button class="ml-4">
                                {{ __('Update Vendor') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const typeSelector = document.getElementById('type_id');
            const passwordFields = document.querySelectorAll('.password-fields');
            
            // Function to check if password fields should be shown
            function checkPasswordFields() {
                const selectedOption = typeSelector.options[typeSelector.selectedIndex];
                
                if (!selectedOption.value) {
                    // No type selected, show password fields by default
                    showPasswordFields(true);
                    return;
                }
                
                const isOnSite = selectedOption.getAttribute('data-is-onsite') === '1';
                // Check vendor type - show password only for on-site vendors
                showPasswordFields(isOnSite);
            }
            
            function showPasswordFields(show) {
                passwordFields.forEach(field => {
                    field.style.display = show ? 'block' : 'none';
                });
            }
            
            // Run on page load
            checkPasswordFields();
            
            // Run whenever vendor type changes
            typeSelector.addEventListener('change', checkPasswordFields);
        });
    </script>
</x-app-layout> 