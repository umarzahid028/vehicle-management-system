<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-tight">System Settings</h2>
                <p class="text-sm text-muted-foreground">Configure system-wide settings and preferences.</p>
            </div>
        </div>
    </x-slot>

    <div class="container mx-auto space-y-6">
        <form action="{{ route('admin.system-settings.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            <div class="rounded-lg border bg-card text-card-foreground shadow-sm mx-4 sm:mx-6 lg:mx-8">
                <div class="p-6 space-y-6">
                    <!-- General Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium">General Settings</h3>
                        
                        <!-- Site Name -->
                        <div class="grid gap-2">
                            <label for="site_name" class="text-sm font-medium leading-none">
                                Site Name
                            </label>
                            <input type="text" id="site_name" name="site_name" 
                                value="{{ old('site_name', $settings->site_name) }}"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                            @error('site_name')
                                <p class="text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Contact Email -->
                        <div class="grid gap-2">
                            <label for="contact_email" class="text-sm font-medium leading-none">
                                Contact Email
                            </label>
                            <input type="email" id="contact_email" name="contact_email" 
                                value="{{ old('contact_email', $settings->contact_email) }}"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                            @error('contact_email')
                                <p class="text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Vehicle Import Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium">Vehicle Import Settings</h3>
                        
                        <!-- Vehicle Imports Path -->
                        <div class="grid gap-2">
                            <label for="vehicle_imports_path" class="text-sm font-medium leading-none">
                                Vehicle Imports Directory
                            </label>
                            <input type="text" id="vehicle_imports_path" name="vehicle_imports_path" 
                                value="{{ old('vehicle_imports_path', $settings->vehicle_imports_path) }}"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                            <p class="text-sm text-muted-foreground">
                                The directory path where vehicle import CSV files will be read from.
                            </p>
                            @error('vehicle_imports_path')
                                <p class="text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Localization Settings -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium">Localization</h3>
                        
                        <!-- Timezone -->
                        <div class="grid gap-2">
                            <label for="timezone" class="text-sm font-medium leading-none">
                                Timezone
                            </label>
                            <select id="timezone" name="timezone" 
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                                @foreach(timezone_identifiers_list() as $timezone)
                                    <option value="{{ $timezone }}" {{ old('timezone', $settings->timezone) === $timezone ? 'selected' : '' }}>
                                        {{ $timezone }}
                                    </option>
                                @endforeach
                            </select>
                            @error('timezone')
                                <p class="text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Date Format -->
                        <div class="grid gap-2">
                            <label for="date_format" class="text-sm font-medium leading-none">
                                Date Format
                            </label>
                            <select id="date_format" name="date_format" 
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                                <option value="Y-m-d" {{ old('date_format', $settings->date_format) === 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD</option>
                                <option value="m/d/Y" {{ old('date_format', $settings->date_format) === 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY</option>
                                <option value="d/m/Y" {{ old('date_format', $settings->date_format) === 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY</option>
                                <option value="M j, Y" {{ old('date_format', $settings->date_format) === 'M j, Y' ? 'selected' : '' }}>MMM D, YYYY</option>
                            </select>
                            @error('date_format')
                                <p class="text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Time Format -->
                        <div class="grid gap-2">
                            <label for="time_format" class="text-sm font-medium leading-none">
                                Time Format
                            </label>
                            <select id="time_format" name="time_format" 
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                                <option value="H:i" {{ old('time_format', $settings->time_format) === 'H:i' ? 'selected' : '' }}>24 Hour (HH:MM)</option>
                                <option value="h:i A" {{ old('time_format', $settings->time_format) === 'h:i A' ? 'selected' : '' }}>12 Hour (HH:MM AM/PM)</option>
                            </select>
                            @error('time_format')
                                <p class="text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 p-6 border-t">
                    <button type="submit" class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-primary text-primary-foreground hover:bg-primary/90 h-10 px-4 py-2">
                        Save Settings
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-app-layout> 