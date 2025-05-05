<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Vendor Type') }}: {{ $vendorType->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route('vendor-types.update', $vendorType) }}">
                        @csrf
                        @method('PUT')

                        <!-- Name -->
                        <div class="mb-4">
                            <x-input-label for="name" :value="__('Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $vendorType->name)" required autofocus />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="3" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $vendorType->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Options -->
                        <div class="mb-4">
                            <div class="flex items-center">
                                <input id="is_on_site" name="is_on_site" type="checkbox" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" {{ old('is_on_site', $vendorType->is_on_site) ? 'checked' : '' }}>
                                <label for="is_on_site" class="ml-2 block text-sm text-gray-900">Is On-Site Vendor</label>
                            </div>
                            <div class="flex items-center mt-2">
                                <input id="has_system_access" name="has_system_access" type="checkbox" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" {{ old('has_system_access', $vendorType->has_system_access) ? 'checked' : '' }}>
                                <label for="has_system_access" class="ml-2 block text-sm text-gray-900">Has System Access</label>
                                <p class="ml-2 text-xs text-gray-500">(On-site vendors always have system access)</p>
                            </div>
                            <div class="flex items-center mt-2">
                                <input id="is_active" name="is_active" type="checkbox" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" {{ old('is_active', $vendorType->is_active) ? 'checked' : '' }}>
                                <label for="is_active" class="ml-2 block text-sm text-gray-900">Is Active</label>
                            </div>
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            <a href="{{ route('vendor-types.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 active:bg-gray-300 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150 mr-2">
                                Cancel
                            </a>
                            <x-primary-button>
                                {{ __('Update') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const isOnSiteCheckbox = document.getElementById('is_on_site');
            const hasSystemAccessCheckbox = document.getElementById('has_system_access');
            
            // Function to update system access based on on-site status
            function updateSystemAccess() {
                if (isOnSiteCheckbox.checked) {
                    // On-site vendors must have system access
                    hasSystemAccessCheckbox.checked = true;
                    hasSystemAccessCheckbox.disabled = true;
                } else {
                    // Off-site vendors can optionally have system access
                    hasSystemAccessCheckbox.disabled = false;
                }
            }
            
            // Run on page load
            updateSystemAccess();
            
            // Listen for changes to the on-site checkbox
            isOnSiteCheckbox.addEventListener('change', updateSystemAccess);
        });
    </script>
</x-app-layout> 