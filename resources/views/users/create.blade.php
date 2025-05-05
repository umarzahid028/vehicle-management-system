<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-tight">Create User</h2>
                <p class="text-sm text-muted-foreground">Add a new user to the system.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2">
                <x-heroicon-o-arrow-left class="mr-2 h-4 w-4" />
                Back
            </a>
        </div>
    </x-slot>

    <div class="container mx-auto space-y-6">
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm mx-4 sm:mx-6 lg:mx-8">
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- User Details -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium">User Details</h3>
                            </div>
                            <div class="grid gap-4">
                                <div class="grid gap-2">
                                    <label for="name" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Name</label>
                                    <div class="relative">
                                        <input type="text" id="name" name="name" value="{{ old('name') }}" 
                                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                            placeholder="Enter name" required>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <x-heroicon-o-user class="h-4 w-4 text-gray-400" />
                                        </div>
                                    </div>
                                    @error('name')
                                        <p class="text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid gap-2">
                                    <label for="email" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Email</label>
                                    <div class="relative">
                                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                            placeholder="Enter email" required>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <x-heroicon-o-envelope class="h-4 w-4 text-gray-400" />
                                        </div>
                                    </div>
                                    @error('email')
                                        <p class="text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid gap-2">
                                    <label for="password" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Password</label>
                                    <div class="relative">
                                        <input type="password" id="password" name="password"
                                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                            placeholder="Enter password" required>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <x-heroicon-o-key class="h-4 w-4 text-gray-400" />
                                        </div>
                                    </div>
                                    @error('password')
                                        <p class="text-sm text-red-500">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid gap-2">
                                    <label for="password_confirmation" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Confirm Password</label>
                                    <div class="relative">
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                                            placeholder="Confirm password" required>
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <x-heroicon-o-key class="h-4 w-4 text-gray-400" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Roles -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="text-lg font-medium">Roles & Permissions</h3>
                            </div>
                            <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2">
                                @foreach($roles as $role)
                                    <div class="rounded-lg border bg-card">
                                        <div class="flex items-center space-x-4 p-4 border-b">
                                            <div class="flex h-5 items-center">
                                                <input type="checkbox" name="roles[]" id="role_{{ $role->id }}" value="{{ $role->id }}"
                                                    {{ (old('roles') && in_array($role->id, old('roles'))) ? 'checked' : '' }}
                                                    class="h-4 w-4 rounded border-gray-300 text-primary focus:ring-2 focus:ring-primary">
                                            </div>
                                            <label for="role_{{ $role->id }}" class="text-sm font-medium">{{ $role->name }}</label>
                                        </div>
                                        @if($role->permissions->count() > 0)
                                            <div class="p-4 bg-muted/40">
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    @foreach(['view', 'create', 'edit', 'delete'] as $action)
                                                        @php
                                                            $actionPermissions = $role->permissions->filter(function($permission) use ($action) {
                                                                return explode(' ', $permission->name)[0] === $action;
                                                            });
                                                        @endphp
                                                        @if($actionPermissions->count() > 0)
                                                            <div class="space-y-2">
                                                                <div class="flex items-center">
                                                                    <span class="text-xs font-medium text-muted-foreground capitalize">{{ $action }}</span>
                                                                </div>
                                                                <div class="grid grid-cols-2 gap-3">
                                                                    @foreach($actionPermissions as $permission)
                                                                        <div class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium
                                                                            {{ $action === 'view' ? 'bg-blue-50 text-blue-700 ring-1 ring-inset ring-blue-700/10' : 
                                                                               ($action === 'create' ? 'bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20' : 
                                                                               ($action === 'edit' ? 'bg-yellow-50 text-yellow-700 ring-1 ring-inset ring-yellow-600/20' : 
                                                                               ($action === 'delete' ? 'bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/10' : 
                                                                               'bg-gray-50 text-gray-700 ring-1 ring-inset ring-gray-600/20'))) }}">
                                                                            {{ explode(' ', $permission->name, 2)[1] ?? $permission->name }}
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                                @error('roles')
                                    <p class="text-sm text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end p-6 border-t">
                    <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout> 