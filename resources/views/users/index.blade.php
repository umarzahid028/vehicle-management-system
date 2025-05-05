<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-tight">Users</h2>
                <p class="text-sm text-muted-foreground">Manage system users and their roles.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                <x-heroicon-o-plus class="mr-2 h-4 w-4" />
                New User
            </a>
        </div>
    </x-slot>

    

    <div class="container mx-auto space-y-6">
        <!-- Search and Filter Section -->
        <div class="rounded-lg border bg-card">
            <form action="{{ route('admin.users.index') }}" method="GET" class="p-4 space-y-4 sm:flex sm:items-center sm:justify-between sm:space-y-0">
                <div class="flex items-center gap-4">
                    <!-- Search -->
                    <div class="relative">
                        <x-heroicon-o-magnifying-glass class="absolute left-2.5 top-2.5 h-4 w-4 text-muted-foreground/70" />
                        <input type="search" 
                            name="search" 
                            value="{{ request('search') }}"
                            placeholder="Search users..." 
                            class="flex h-9 w-full sm:w-[300px] rounded-md border border-input bg-background px-3 py-2 pl-9 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                    </div>

                    <!-- Role Filter -->
                    <select name="role" 
                        onchange="this.form.submit()"
                        class="flex h-9 w-full sm:w-[200px] rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                        <option value="">All Roles</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ request('role') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Search Button -->
                <div class="flex items-center gap-4">
                    <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-9 px-4 py-2">
                        Search
                    </button>
                    @if(request()->hasAny(['search', 'role']))
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-9 px-4 py-2">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Users Table -->
        <div class="rounded-lg border bg-card">
            <div class="relative w-full overflow-auto">
                <table class="w-full caption-bottom text-sm">
                    <thead class="[&_tr]:border-b">
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Name</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Email</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Roles</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Status</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Last Login</th>
                            <th class="h-12 px-4 align-middle font-medium text-muted-foreground text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:border-0">
                        @foreach($users as $user)
                            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                <td class="p-4 align-middle font-medium">
                                    <div class="flex items-center gap-3">
                                        <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-full">
                                            @if($user->avatar)
                                                <img src="{{ $user->avatar }}" alt="{{ $user->name }}" class="h-full w-full object-cover" />
                                            @else
                                                <span class="flex h-full w-full items-center justify-center rounded-full bg-primary/10 text-primary font-medium">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </span>
                                            @endif
                                            <span class="absolute right-0 bottom-0 h-2 w-2 rounded-full border-2 border-background {{ $user->email_verified_at ? 'bg-green-400' : 'bg-yellow-400' }}"></span>
                                        </span>
                                        <div>
                                            <p class="text-sm font-medium leading-none">{{ $user->name }}</p>
                                            <p class="text-xs text-muted-foreground">Created {{ $user->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 align-middle">{{ $user->email }}</td>
                                <td class="p-4 align-middle">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->roles as $role)
                                            <span class="inline-flex items-center rounded-md bg-primary/10 px-2 py-1 text-xs font-medium text-primary ring-1 ring-inset ring-primary/20">
                                                {{ $role->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="p-4 align-middle">
                                    <span @class([
                                        'inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset',
                                        'bg-green-50 text-green-700 ring-green-600/20' => $user->email_verified_at,
                                        'bg-yellow-50 text-yellow-700 ring-yellow-600/20' => !$user->email_verified_at,
                                    ])>
                                        {{ $user->email_verified_at ? 'Active' : 'Pending' }}
                                    </span>
                                </td>
                                <td class="p-4 align-middle text-muted-foreground">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                </td>
                                <td class="p-4 align-middle">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                            class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input hover:bg-accent hover:text-accent-foreground h-8 w-8"
                                            title="Edit User">
                                            <x-heroicon-o-pencil class="h-4 w-4" />
                                            <span class="sr-only">Edit</span>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 border border-input hover:bg-destructive hover:text-destructive-foreground h-8 w-8"
                                                onclick="return confirm('Are you sure you want to delete this user?')"
                                                title="Delete User">
                                                <x-heroicon-o-trash class="h-4 w-4" />
                                                <span class="sr-only">Delete</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($users->isEmpty())
                <div class="flex items-center justify-center p-8 text-center">
                    <div class="space-y-2">
                        <x-heroicon-o-users class="mx-auto h-12 w-12 text-muted-foreground/60" />
                        <h3 class="text-lg font-medium">No users found</h3>
                        <p class="text-sm text-muted-foreground">Get started by creating a new user.</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Pagination -->
        @if($users->hasPages())
            <div>
                {{ $users->links() }}
            </div>
        @endif
    </div>


</x-app-layout> 