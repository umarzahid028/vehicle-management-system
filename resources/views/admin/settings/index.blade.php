<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            System Settings
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <!-- Company Name -->
                        <div>
                            <x-input-label for="company_name" value="Company Name" />
                            <x-text-input id="company_name" name="company_name" type="text" class="mt-1 block w-full"
                                :value="old('company_name', $settings['company_name'])" required autofocus />
                            <x-input-error class="mt-2" :messages="$errors->get('company_name')" />
                        </div>

                        <!-- Notification Email -->
                        <div>
                            <x-input-label for="notification_email" value="Notification Email" />
                            <x-text-input id="notification_email" name="notification_email" type="email" class="mt-1 block w-full"
                                :value="old('notification_email', $settings['notification_email'])" required />
                            <x-input-error class="mt-2" :messages="$errors->get('notification_email')" />
                        </div>

                        <!-- Enable Notifications -->
                        <div class="flex items-center">
                            <input type="checkbox" name="enable_notifications" id="enable_notifications"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                {{ old('enable_notifications', $settings['enable_notifications']) ? 'checked' : '' }}>
                            <x-input-label for="enable_notifications" value="Enable System Notifications" class="ml-2" />
                        </div>

                        <!-- Enable Auto Assignments -->
                        <div class="flex items-center">
                            <input type="checkbox" name="enable_auto_assignments" id="enable_auto_assignments"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                {{ old('enable_auto_assignments', $settings['enable_auto_assignments']) ? 'checked' : '' }}>
                            <x-input-label for="enable_auto_assignments" value="Enable Auto Assignments" class="ml-2" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>Save Settings</x-primary-button>

                            @if (session('success'))
                                <p class="text-sm text-green-600">{{ session('success') }}</p>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 