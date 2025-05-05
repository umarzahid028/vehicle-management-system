<div>
    <form method="POST" action="{{ isset($salesTeam) ? route('sales-team.update', $salesTeam) : route('sales-team.store') }}" enctype="multipart/form-data">
        @csrf
        @if(isset($salesTeam))
            @method('PUT')
        @endif

        <div class="space-y-8">
            <!-- Basic Information Section -->
            <div class="border-b border-gray-200 pb-8">
                <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-6">
                    <!-- Name -->
                    <div class="sm:col-span-2">
                        <x-input-label for="name" :value="__('Full Name')" class="text-sm font-medium text-gray-900" />
                        <div class="mt-2 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </div>
                            <x-text-input id="name" name="name" type="text" 
                                class="pl-10 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                :value="old('name', $salesTeam->name ?? '')" 
                                required autofocus autocomplete="name" 
                                placeholder="Enter team member's full name" />
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('name')" />
                    </div>

                    <!-- Position -->
                    <div class="sm:col-span-2">
                        <x-input-label for="position" :value="__('Position')" class="text-sm font-medium text-gray-900" />
                        <div class="mt-2 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 7H4a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Z"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                            </div>
                            <x-text-input id="position" name="position" type="text" 
                                class="pl-10 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                :value="old('position', $salesTeam->position ?? '')" 
                                required placeholder="e.g. Sales Representative" />
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('position')" />
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-sm font-medium text-gray-900 mb-4">Contact Information</h3>
                <div class="grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-6">
                    <!-- Email -->
                    <div>
                        <x-input-label for="email" :value="__('Email Address')" class="text-sm font-medium text-gray-900" />
                        <div class="mt-2 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            </div>
                            <x-text-input id="email" name="email" type="email" 
                                class="pl-10 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                :value="old('email', $salesTeam->email ?? '')" 
                                required placeholder="email@example.com" />
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <!-- Phone -->
                    <div>
                        <x-input-label for="phone" :value="__('Phone Number')" class="text-sm font-medium text-gray-900" />
                        <div class="mt-2 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </div>
                            <x-text-input id="phone" name="phone" type="tel" 
                                class="pl-10 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                                :value="old('phone', $salesTeam->phone ?? '')" 
                                placeholder="(555) 555-5555" />
                        </div>
                        <x-input-error class="mt-2" :messages="$errors->get('phone')" />
                    </div>
                </div>
            </div>

            <!-- Additional Information Section -->
            <div class="border-b border-gray-200 pb-8">
                <h3 class="text-sm font-medium text-gray-900 mb-4">Additional Information</h3>
                
                <!-- Bio -->
                <div class="mb-6">
                    <x-input-label for="bio" :value="__('Bio')" class="text-sm font-medium text-gray-900" />
                    <div class="mt-2 relative">
                        <div class="absolute top-3 left-0 pl-3 flex items-start pointer-events-none text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
                        </div>
                        <textarea id="bio" name="bio" rows="4" 
                            class="pl-10 mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" 
                            placeholder="Brief description about the team member...">{{ old('bio', $salesTeam->bio ?? '') }}</textarea>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">Brief description about the team member's experience and role.</p>
                    <x-input-error class="mt-2" :messages="$errors->get('bio')" />
                </div>

                <!-- Photo Upload -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-900 mb-2">Profile Photo</label>
                    <div class="flex items-center space-x-6">
                        @if(isset($salesTeam) && $salesTeam->photo_path)
                            <div class="shrink-0">
                                <img src="{{ Storage::url($salesTeam->photo_path) }}" alt="{{ $salesTeam->name }}" 
                                    class="h-16 w-16 object-cover rounded-full" />
                            </div>
                        @endif
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h7"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/><path d="m15 12 2-2"/><path d="M8 9a2 2 0 1 0 0-4 2 2 0 0 0 0 4z"/></svg>
                            </div>
                            <input type="file" id="photo" name="photo" accept="image/*" 
                                class="pl-10 block w-full text-sm text-gray-500 rounded-md border-gray-300 shadow-sm
                                file:mr-4 file:py-2 file:px-4
                                file:rounded-md file:border-0
                                file:text-sm file:font-semibold
                                file:bg-indigo-50 file:text-indigo-700
                                hover:file:bg-indigo-100" />
                        </div>
                    </div>
                    <p class="mt-2 text-sm text-gray-500">PNG, JPG, GIF up to 10MB</p>
                    <x-input-error class="mt-2" :messages="$errors->get('photo')" />
                </div>

                <!-- Active Status -->
                <div class="flex items-start">
                    <div class="flex items-center h-5">
                        <input type="checkbox" id="is_active" name="is_active" value="1" 
                            class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" 
                            {{ old('is_active', $salesTeam->is_active ?? true) ? 'checked' : '' }}>
                    </div>
                    <div class="ml-3 text-sm">
                        <label for="is_active" class="font-medium text-gray-900">Active Status</label>
                        <p class="text-gray-500">Set whether this team member is currently active</p>
                    </div>
                </div>
            </div>

            <!-- Password Section -->
            <div class="mt-8 space-y-6">
                <h3 class="text-lg font-medium text-gray-900">Password</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-password-input
                        id="password"
                        name="password"
                        label="Password"
                        required
                        placeholder="Enter password"
                        helpText="Password must be at least 8 characters and contain uppercase, lowercase, numbers and special characters"
                    />

                    <x-password-input
                        id="password_confirmation"
                        name="password_confirmation"
                        label="Confirm Password"
                        required
                        placeholder="Confirm your password"
                        helpText="Please re-enter your password to confirm"
                    />
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-4">
                <a href="{{ route('sales-team.index') }}" 
                    class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    Cancel
                </a>
                <button type="submit" 
                    class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    {{ isset($salesTeam) ? __('Update Team Member') : __('Add Team Member') }}
                </button>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function togglePasswordVisibility(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.parentElement.querySelector('.password-toggle');
    
    if (field.type === 'password') {
        field.type = 'text';
        button.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-10-7-10-7a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 10 7 10 7a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>';
    } else {
        field.type = 'password';
        button.innerHTML = '<path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>';
    }
}
</script>
@endpush 