<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-tight">Settings</h2>
                <p class="text-sm text-muted-foreground">Manage system settings and configurations.</p>
            </div>
        </div>
    </x-slot>

    <div class="container mx-auto space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 px-4 sm:px-6 lg:px-8">
            <!-- User Management Card -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="p-2 rounded-lg bg-primary/10">
                            <x-heroicon-o-users class="w-6 h-6 text-primary" />
                        </div>
                        <div>
                            <h3 class="font-semibold">User Management</h3>
                            <p class="text-sm text-muted-foreground">Manage system users and access</p>
                        </div>
                    </div>
                    <div class="pt-4">
                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 w-full">
                            Manage Users
                            <x-heroicon-o-arrow-right class="ml-2 h-4 w-4" />
                        </a>
                    </div>
                </div>
            </div>

            <!-- Activity Log Card -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="p-2 rounded-lg bg-primary/10">
                            <x-heroicon-o-clock class="w-6 h-6 text-primary" />
                        </div>
                        <div>
                            <h3 class="font-semibold">Activity Log</h3>
                            <p class="text-sm text-muted-foreground">View system activity and audit logs</p>
                        </div>
                    </div>
                    <div class="pt-4">
                        <a href="{{ route('admin.activity-log.index') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 w-full">
                            View Logs
                            <x-heroicon-o-arrow-right class="ml-2 h-4 w-4" />
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Settings Card -->
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
                <div class="p-6 space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="p-2 rounded-lg bg-primary/10">
                            <x-heroicon-o-cog-6-tooth class="w-6 h-6 text-primary" />
                        </div>
                        <div>
                            <h3 class="font-semibold">System Settings</h3>
                            <p class="text-sm text-muted-foreground">Configure system preferences</p>
                        </div>
                    </div>
                    <div class="pt-4">
                        <a href="{{ route('admin.system-settings.index') }}" class="inline-flex items-center justify-center rounded-md text-sm font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 border border-input bg-background hover:bg-accent hover:text-accent-foreground h-10 px-4 py-2 w-full">
                            Configure Settings
                            <x-heroicon-o-arrow-right class="ml-2 h-4 w-4" />
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 