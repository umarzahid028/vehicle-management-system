<x-app-layout>
    <x-slot name="header">
        <!-- Breadcrumb and Title -->
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="space-y-1.5">
               
                <div class="flex items-center gap-4">
                    <div>
                        <h1 class="text-2xl font-semibold tracking-tight">{{ $user->name }}</h1>
                        <p class="text-sm text-muted-foreground">{{ $user->email }}</p>
                    </div>
                    <span @class([
                        'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                        'bg-green-50 text-green-700 ring-green-600/20' => $user->email_verified_at,
                        'bg-yellow-50 text-yellow-700 ring-yellow-600/20' => !$user->email_verified_at,
                    ])>
                        {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.users.index') }}" 
                    class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 hover:bg-accent hover:text-accent-foreground h-9 px-3">
                    <x-heroicon-o-arrow-left class="mr-2 h-4 w-4" />
                    Back
                </a>
                <div class="flex items-center gap-2">
                    @can('delete', $user)
                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input hover:bg-destructive hover:text-destructive-foreground h-9 px-3"
                                onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                <x-heroicon-o-trash class="mr-2 h-4 w-4" />
                                Delete User
                            </button>
                        </form>
                    @endcan
                    @if(!$user->email_verified_at)
                        <form action="{{ route('admin.users.verify', $user) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" 
                                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-3">
                                <x-heroicon-o-check-badge class="mr-2 h-4 w-4" />
                                Verify Email
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-[2fr,1fr] gap-6 px-4 sm:px-6 lg:px-8">
        <!-- Main Form -->
        <div class="space-y-6">
            <form action="{{ route('admin.users.update', $user) }}" method="POST" class="">
                @csrf
                @method('PUT')
                
                <!-- User Information Card -->
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                    <div class="p-6 space-y-4">
                        <h3 class="text-lg font-medium">User Information</h3>
                        
                        <div class="space-y-4">
                            <!-- Name -->
                            <div class="space-y-2">
                                <label for="name" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Full Name</label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" />
                                @error('name')
                                    <p class="text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Email -->
                            <div class="space-y-2">
                                <label for="email" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Email Address</label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" />
                                @error('email')
                                    <p class="text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password -->
                            <div class="space-y-2">
                                <label for="password" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">New Password</label>
                                <input type="password" id="password" name="password" 
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    placeholder="Leave blank to keep current password" />
                                @error('password')
                                    <p class="text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Password Confirmation -->
                            <div class="space-y-2">
                                <label for="password_confirmation" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Confirm New Password</label>
                                <input type="password" id="password_confirmation" name="password_confirmation" 
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Roles & Permissions -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                <div class="p-6 space-y-6">
                    <div>
                        <h3 class="text-lg font-medium">Role & Permissions</h3>
                        <p class="text-sm text-muted-foreground">Manage user roles and their associated permissions</p>
                    </div>

                    <!-- Role Selection -->
                    <div class="space-y-6">
                        <div class="space-y-4">
                            <div class="space-y-2">
                                <label for="role" class="text-sm font-medium leading-none">Assigned Role</label>
                                <select name="roles[]" id="role" 
                                    class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                    onchange="updatePermissions(this.value)">
                                    <option value="">Select a role...</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->id }}" 
                                            {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'selected' : '' }}>
                                            {{ \App\Enums\Role::tryFrom($role->name)?->label() ?? $role->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Permissions Display -->
                            <div id="permissions-container" class="space-y-4">
                                <div class="flex flex-wrap gap-2">
                                    @php
                                        $basePermissions = ['view any', 'view own', 'create', 'edit', 'delete', 'manage'];
                                        $modules = [
                                            'users' => ['view users', 'create users', 'edit users', 'delete users', 'manage users'],
                                            'roles' => ['view roles', 'create roles', 'edit roles', 'delete roles', 'assign roles'],
                                            'permissions' => ['view permissions', 'manage permissions'],
                                            'sales team' => ['view sales team', 'create sales team', 'edit sales team', 'delete sales team', 'manage sales team'],
                                            'vehicles' => ['view vehicles', 'create vehicles', 'edit vehicles', 'delete vehicles', 'manage vehicles', 'archive vehicles', 'bulk edit vehicles'],
                                            'transports' => ['view transports', 'create transports', 'edit transports', 'delete transports', 'manage transports'],
                                            'inspections' => ['view inspections', 'create inspections', 'edit inspections', 'delete inspections', 'manage inspections', 'configure stages'],
                                            'vendors' => ['view vendors', 'create vendors', 'edit vendors', 'delete vendors', 'manage vendors'],
                                            'sales issues' => ['view sales issues', 'create sales issues', 'edit sales issues', 'delete sales issues', 'manage sales issues', 'review sales issues'],
                                            'goodwill claims' => ['view goodwill claims', 'create goodwill claims', 'edit goodwill claims', 'delete goodwill claims', 'manage goodwill claims', 'approve goodwill claims', 'reject goodwill claims'],
                                            'timeline' => ['view timeline', 'add timeline entries', 'manage timeline'],
                                            'tags' => ['view tags', 'create tags', 'edit tags', 'delete tags', 'assign tags'],
                                            'alerts' => ['view alerts', 'create alerts', 'manage alerts'],
                                            'photos' => ['view photos', 'upload photos', 'delete photos', 'manage photos'],
                                            'checklists' => ['view checklists', 'create checklists', 'edit checklists', 'delete checklists', 'complete checklists', 'manage checklists'],
                                            'notifications' => ['view notifications', 'manage notifications', 'manage notification settings'],
                                            'reports' => ['view reports', 'generate reports', 'export data'],
                                            'system' => ['manage system settings']
                                        ];
                                    @endphp

                                    @foreach($modules as $module => $permissions)
                                        @foreach($permissions as $permission)
                                            <label class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 hover:bg-gray-200 cursor-pointer select-none">
                                                <input type="checkbox" 
                                                    name="permissions[]" 
                                                    value="{{ $permission }}"
                                                    class="sr-only peer"
                                                    {{ $user->hasPermissionTo($permission) ? 'checked' : '' }}>
                                                <span class="peer-checked:bg-primary peer-checked:text-white px-2.5 py-0.5 rounded-full transition-colors">
                                                    {{ $permission }}
                                                </span>
                                            </label>
                                        @endforeach
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Save Changes Button -->
                        <div class="flex justify-end pt-4 border-t">
                            <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                                <x-heroicon-m-check class="mr-2 h-4 w-4" />
                                Save changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Account Status Card -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                <div class="p-6 space-y-4">
                    <h3 class="text-lg font-medium">Account Status</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="space-y-0.5">
                                <div class="text-sm font-medium">Email Verification</div>
                                <div class="text-xs text-muted-foreground">
                                    {{ $user->email_verified_at ? 'Verified on ' . $user->email_verified_at->format('M d, Y') : 'Not verified' }}
                                </div>
                            </div>
                            <span @class([
                                'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                'bg-green-50 text-green-700 ring-green-600/20' => $user->email_verified_at,
                                'bg-yellow-50 text-yellow-700 ring-yellow-600/20' => !$user->email_verified_at,
                            ])>
                                {{ $user->email_verified_at ? 'Verified' : 'Pending' }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="space-y-0.5">
                                <div class="text-sm font-medium">Last Login</div>
                                <div class="text-xs text-muted-foreground">
                                    {{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : 'Never' }}
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="space-y-0.5">
                                <div class="text-sm font-medium">Account Created</div>
                                <div class="text-xs text-muted-foreground">
                                    {{ $user->created_at->format('M d, Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
function updatePermissions(roleId) {
    if (!roleId) return;

    // Here you would typically make an AJAX call to fetch the role's permissions
    fetch(`/admin/roles/${roleId}/permissions`)
        .then(response => response.json())
        .then(permissions => {
            // Clear all checkboxes first
            document.querySelectorAll('input[name="permissions[]"]').forEach(checkbox => {
                checkbox.checked = false;
            });

            // Check the boxes for the permissions associated with the role
            permissions.forEach(permission => {
                const checkbox = document.querySelector(`input[name="permissions[]"][value="${permission}"]`);
                if (checkbox) checkbox.checked = true;
            });
        });
}
</script>
@endpush 