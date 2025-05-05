<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-tight">Edit Role</h2>
                <p class="text-sm text-muted-foreground">Modify role details and manage permissions.</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.roles.index') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                    <x-heroicon-o-arrow-left class="mr-2 h-4 w-4" />
                    Back to Roles
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6 py-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <form action="{{ route('admin.roles.update', $role->id) }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Role Information Card -->
                        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                            <div class="p-6 space-y-4">
                                <h3 class="text-lg font-medium">Role Information</h3>
                                
                                <div class="space-y-4">
                                    <!-- Name -->
                                    <div class="space-y-2">
                                        <label for="name" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Role Name</label>
                                        <div class="relative">
                                            <input type="text" id="name" name="name" value="{{ old('name', $role->name) }}" 
                                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                                {{ in_array($role->name, ['admin']) ? 'readonly' : '' }} />
                                            
                                            @if(in_array($role->name, ['admin']))
                                                <div class="absolute right-3 top-1/2 transform -translate-y-1/2">
                                                    <span class="inline-flex items-center rounded-md bg-amber-50 px-2 py-1 text-xs font-medium text-amber-800 ring-1 ring-inset ring-amber-600/20">
                                                        <svg class="mr-1 h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd" />
                                                        </svg>
                                                        Protected
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        @error('name')
                                            <p class="text-sm text-red-500">{{ $message }}</p>
                                        @enderror
                                        @if(in_array($role->name, ['admin']))
                                            <p class="text-xs text-muted-foreground mt-1">System roles cannot be renamed for security reasons.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Permissions Card -->
                        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                            <div class="p-6 space-y-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-medium">Permissions</h3>
                                    <div class="flex items-center gap-2">
                                        <button type="button" id="selectAll" class="inline-flex items-center rounded-md bg-primary/10 px-2 py-1 text-xs font-medium text-primary ring-1 ring-inset ring-primary/20 hover:bg-primary/20">
                                            <svg class="mr-1 h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                            Select All
                                        </button>
                                        <button type="button" id="deselectAll" class="inline-flex items-center rounded-md bg-gray-50 px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 hover:bg-gray-100">
                                            <svg class="mr-1 h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                            Deselect All
                                        </button>
                                    </div>
                                </div>

                                <!-- Permissions Groups -->
                                <div class="space-y-6">
                                    @foreach($permissionGroups as $resource => $permissions)
                                        <div class="border rounded-lg overflow-hidden">
                                            <div class="flex items-center justify-between bg-muted/40 px-4 py-2">
                                                <h4 class="text-sm font-semibold capitalize">{{ $resource }}</h4>
                                                <button type="button" data-resource="{{ $resource }}" class="toggle-resource inline-flex items-center rounded-md bg-white px-2 py-1 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 hover:bg-gray-50">
                                                    <svg class="mr-1 h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M14.5 10a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm-4.5 1.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" clip-rule="evenodd" />
                                                    </svg>
                                                    Toggle Group
                                                </button>
                                            </div>
                                            <div class="p-4 grid grid-cols-1 md:grid-cols-3 gap-3">
                                                @foreach($permissions as $permission)
                                                    <label class="relative flex items-start cursor-pointer group">
                                                        <div class="flex items-center h-5">
                                                            <input type="checkbox" 
                                                                name="permissions[]" 
                                                                id="permission_{{ $permission['id'] }}"
                                                                value="{{ $permission['name'] }}" 
                                                                class="peer sr-only" 
                                                                data-resource="{{ $resource }}"
                                                                {{ in_array($permission['name'], $rolePermissions) ? 'checked' : '' }}
                                                                {{ $role->name === 'admin' ? 'disabled' : '' }}>
                                                            <div class="w-4 h-4 rounded border border-gray-300 peer-focus:ring-2 peer-focus:ring-primary peer-checked:bg-primary peer-checked:border-primary peer-disabled:opacity-50 peer-disabled:bg-gray-200"></div>
                                                        </div>
                                                        <div class="ml-2 text-sm leading-tight">
                                                            <span class="font-medium text-gray-700 peer-checked:text-primary-600 peer-disabled:text-gray-400">
                                                                {{ ucfirst($permission['action']) }}
                                                            </span>
                                                            <p class="text-xs text-gray-500 peer-disabled:text-gray-400">
                                                                {{ ucfirst($permission['action']) }} {{ $resource }}
                                                            </p>
                                                        </div>
                                                        <div class="absolute inset-0 rounded-md peer-checked:bg-primary/5 pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity"></div>
                                                    </label>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end pt-6">
                        <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2 shadow-sm"
                            {{ $role->name === 'admin' ? 'disabled' : '' }}>
                            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to update visual state of checkboxes
            function updateVisualCheckbox(checkbox) {
                const visualCheckbox = checkbox.nextElementSibling;
                if (checkbox.checked) {
                    visualCheckbox.classList.add('bg-primary', 'border-primary');
                } else {
                    visualCheckbox.classList.remove('bg-primary', 'border-primary');
                }
            }

            // Select all permissions
            document.getElementById('selectAll').addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
                checkboxes.forEach(function(checkbox) {
                    if (!checkbox.disabled) {
                        checkbox.checked = true;
                        updateVisualCheckbox(checkbox);
                    }
                });
            });

            // Deselect all permissions
            document.getElementById('deselectAll').addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('input[name="permissions[]"]');
                checkboxes.forEach(function(checkbox) {
                    if (!checkbox.disabled) {
                        checkbox.checked = false;
                        updateVisualCheckbox(checkbox);
                    }
                });
            });

            // Toggle permissions by resource
            document.querySelectorAll('.toggle-resource').forEach(function(button) {
                button.addEventListener('click', function() {
                    const resource = this.getAttribute('data-resource');
                    const checkboxes = document.querySelectorAll(`input[data-resource="${resource}"]`);
                    
                    // Check if any are unchecked
                    const anyUnchecked = Array.from(checkboxes).some(function(checkbox) {
                        return !checkbox.checked && !checkbox.disabled;
                    });
                    
                    // Update all checkboxes in the group
                    checkboxes.forEach(function(checkbox) {
                        if (!checkbox.disabled) {
                            checkbox.checked = anyUnchecked;
                            updateVisualCheckbox(checkbox);
                        }
                    });

                    // Update button text
                    if (anyUnchecked) {
                        this.innerHTML = `
                            <svg class="mr-1 h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            Selected
                        `;
                        this.classList.add('bg-primary/10', 'text-primary');
                        this.classList.remove('bg-white', 'text-gray-600');
                    } else {
                        this.innerHTML = `
                            <svg class="mr-1 h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M14.5 10a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zm-4.5 1.5a1.5 1.5 0 100-3 1.5 1.5 0 000 3z" clip-rule="evenodd" />
                            </svg>
                            Toggle Group
                        `;
                        this.classList.remove('bg-primary/10', 'text-primary');
                        this.classList.add('bg-white', 'text-gray-600');
                    }
                });
            });

            // Fix custom checkboxes behavior
            document.querySelectorAll('.relative.flex.items-start.cursor-pointer').forEach(function(label) {
                const checkbox = label.querySelector('input[type="checkbox"]');
                
                // Initialize visual state
                updateVisualCheckbox(checkbox);
                
                // When label is clicked, toggle the checkbox
                label.addEventListener('click', function(event) {
                    // Prevent default behavior only on the label and visual elements
                    // but not on the actual checkbox to avoid double toggling
                    if (event.target !== checkbox && !checkbox.disabled) {
                        event.preventDefault();
                        checkbox.checked = !checkbox.checked;
                        updateVisualCheckbox(checkbox);
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout> 