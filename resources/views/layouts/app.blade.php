<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <!-- Sortable.js for drag and drop functionality -->
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
        
        @yield('styles')
    </head>
    <body class="font-sans antialiased">
        <div x-data="{ sidebarOpen: true }" class="min-h-screen bg-background">
            <div class="flex">
                <!-- Mobile sidebar backdrop -->
                <div 
                    x-show="sidebarOpen" 
                    @click="sidebarOpen = false" 
                    class="fixed inset-0 z-20 bg-black/50 backdrop-blur-sm transition-opacity lg:hidden">
                </div>

                <!-- Sidebar -->
                <div 
                    x-show="sidebarOpen" 
                    class="fixed inset-y-0 left-0 z-30 w-64 transform transition duration-300 lg:relative lg:translate-x-0" 
                    :class="{'translate-x-0': sidebarOpen, '-translate-x-full': !sidebarOpen}">
                    <x-sidebar />
                </div>

                <!-- Content -->
                <div class="flex-1 flex flex-col overflow-hidden">
                    <!-- Top navigation -->
                    <div class="bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60 border-b lg:hidden">
                        <div class="flex items-center justify-between h-16 px-4">
                            <button @click="sidebarOpen = true" class="text-muted-foreground hover:text-foreground focus:outline-none focus:ring-2 focus:ring-inset focus:ring-primary rounded-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                </svg>
                            </button>
                            <a href="{{ route('dashboard') }}" class="flex items-center">
                                <x-application-logo class="block h-9 w-auto fill-current text-foreground" />
                                <span class="ml-2 text-lg font-medium text-foreground">TrevinosAuto</span>
                            </a>
                            <div></div> <!-- Empty div for flex spacing -->
                        </div>
                    </div>

                    <!-- Page Heading -->
                    @isset($header)
                        <header class="sticky top-0 z-50 w-full border-b bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
                            <div class="container mx-auto space-y-6 px-4">
                                <div class="flex h-16 items-center justify-between">
                                    <!-- Left side - Header Title -->
                                    <div class="flex-1">
                                        {{ $header }}
                                    </div>

                                    <!-- Right side - User Menu -->
                                    <div class="flex items-center gap-4 pl-3">
                                        <!-- Notifications Dropdown -->
                                        <div class="ml-auto flex items-center gap-x-4">
                                            <div class="relative">
                                                <button type="button" class="relative rounded-full bg-white p-1 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2" id="notifications-menu-button">
                                                    <span class="sr-only">View notifications</span>
                                                    <x-heroicon-o-bell class="h-6 w-6" />
                                                    @php
                                                        $unreadCount = auth()->user()->unreadNotifications()->count();
                                                    @endphp
                                                    @if($unreadCount > 0)
                                                        <span id="notification-counter" class="absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
                                                            {{ $unreadCount }}
                                                        </span>
                                                    @else
                                                        <span id="notification-counter" class="hidden absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full">
                                                            0
                                                        </span>
                                                    @endif
                                                </button>

                                                <!-- Notifications Dropdown Panel -->
                                                <div class="hidden absolute right-0 z-10 mt-2 w-80 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" id="notifications-dropdown">
                                                    <div class="px-4 py-2 border-b border-gray-100">
                                                        <h3 class="text-sm font-semibold">Notifications</h3>
                                                    </div>
                                                    <div id="notifications-list" class="max-h-96 overflow-y-auto">
                                                        @foreach(auth()->user()->notifications()->latest()->take(5)->get() as $notification)
                                                            <a href="{{ $notification->data['url'] ?? '#' }}" class="block px-4 py-2 hover:bg-gray-100 {{ $notification->read_at ? 'opacity-50' : '' }}">
                                                                <div class="flex items-start">
                                                                    <div class="flex-1">
                                                                        <p class="text-sm font-medium text-gray-900">{{ $notification->data['message'] ?? 'Notification' }}</p>
                                                                        <p class="text-xs text-gray-500">{{ $notification->created_at->diffForHumans() }}</p>
                                                                    </div>
                                                                    @if(!$notification->read_at)
                                                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-600/20">New</span>
                                                                    @endif
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                    @if(auth()->user()->notifications->count() > 0)
                                                        <div class="px-4 py-2 border-t border-gray-100">
                                                            <div class="flex justify-between items-center">
                                                                <a href="{{ route('notifications.index') }}" class="text-sm text-primary hover:text-primary-dark">View all</a>
                                                                @if($unreadCount > 0)
                                                                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="flex items-center">
                                                                        @csrf
                                                                        <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                                                                            Mark all as read
                                                                        </button>
                                                                    </form>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @else
                                                        <div class="px-4 py-2 text-sm text-gray-500 text-center">
                                                            No notifications
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- User Dropdown -->
                                            <x-dropdown align="right" width="56">
                                                <x-slot name="trigger">
                                                    <button class="inline-flex items-center gap-3 px-2 py-1.5 rounded-md bg-background hover:bg-accent text-sm transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring">
                                                        <!-- Avatar -->
                                                        <div class="relative h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-primary">
                                                                {{ substr(Auth::user()->name, 0, 2) }}
                                                            </span>
                                                        </div>
                                                        <div class="flex flex-col items-start">
                                                            <span class="text-sm font-medium">{{ Auth::user()->name }}</span>
                                                            <span class="text-xs text-muted-foreground">{{ \App\Enums\Role::tryFrom(Auth::user()->roles->first()?->name ?? '')?->label() ?? 'User' }}</span>
                                                        </div>
                                                        <svg class="h-4 w-4 text-muted-foreground" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                        </svg>
                                                    </button>
                                                </x-slot>

                                                <x-slot name="content">
                                                    <div class="p-2">
                                                        <!-- User Info -->
                                                        <div class="px-2 py-1.5">
                                                            <p class="text-sm font-medium text-foreground">{{ Auth::user()->email }}</p>
                                                            <p class="text-xs text-muted-foreground mt-0.5">Joined {{ Auth::user()->created_at->format('M Y') }}</p>
                                                        </div>
                                                        
                                                        <div class="my-1 h-px bg-accent"></div>

                                                        <!-- Menu Items -->
                                                        <nav class="space-y-1">
                                                            <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2 px-2 py-1.5 hover:bg-accent rounded-md transition-colors">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                                                    <circle cx="12" cy="7" r="4"/>
                                                                </svg>
                                                                {{ __('Profile') }}
                                                            </x-dropdown-link>

                                                            @role('admin')
                                                            <x-dropdown-link :href="route('admin.settings.index')" class="flex items-center gap-2 px-2 py-1.5 hover:bg-accent rounded-md transition-colors">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                    <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                                                                    <circle cx="12" cy="12" r="3"/>
                                                                </svg>
                                                                {{ __('Settings') }}
                                                            </x-dropdown-link>
                                                            @endrole

                                                            <div class="my-1 h-px bg-accent"></div>

                                                            <!-- Logout -->
                                                            <form method="POST" action="{{ route('logout') }}">
                                                                @csrf
                                                                <x-dropdown-link :href="route('logout')"
                                                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                                                    class="flex items-center gap-2 px-2 py-1.5 text-destructive hover:bg-destructive/10 rounded-md transition-colors">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                                                                        <polyline points="16 17 21 12 16 7"/>
                                                                        <line x1="21" y1="12" x2="9" y2="12"/>
                                                                    </svg>
                                                                    {{ __('Log Out') }}
                                                                </x-dropdown-link>
                                                            </form>
                                                        </nav>
                                                    </div>
                                                </x-slot>
                                            </x-dropdown>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </header>
                    @endisset

                    <!-- Page Content -->
                    <main class="flex-1 overflow-y-auto">
                        <div class="py-6">
                            <div class="max-w-full">
                                {{ $slot }}
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
        
        <!-- Sound initialization elements -->
        <div id="sound-init-container" class="fixed bottom-4 right-4 z-50 flex flex-col gap-2" style="display: none;">
            <button id="init-sound-btn" class="bg-primary text-white p-2 rounded-full shadow-lg hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2">
                <span class="sr-only">Initialize Sound</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15.465a5 5 0 001.06-7.89l5.415-5.415a1 1 0 00-1.414-1.414L6.464 9.88a7 7 0 002.12 11.382l.293.16a1 1 0 001.414-1.414l-3.879-3.89a3 3 0 01-.626-3.314L4.21 14.216a1 1 0 101.414 1.414l-.04-.04z" />
                </svg>
            </button>
            
            
        </div>

        <!-- Additional Scripts -->
        @stack('scripts')
        @yield('scripts')
        <script>
            // Initialize notification listener
            document.addEventListener('DOMContentLoaded', function() {
                // Only initialize for authenticated users
                @auth
                try {
                    // Initialize sound system first to ensure it's ready
                    if (window.VehicleImportNotification && typeof window.VehicleImportNotification.initAudio === 'function') {
                        console.log('Attempting to initialize audio context');
                    }
                    
                    // Show sound initialization button immediately for better UX
                    const soundInitContainer = document.getElementById('sound-init-container');
                    if (soundInitContainer) {
                        soundInitContainer.style.display = 'flex';
                    }
                    
                    // Initialize notification listener
                    if (window.NotificationListener) {
                        console.log('Initializing NotificationListener');
                        const listener = new NotificationListener({{ auth()->id() }});
                        
                        // Store the listener instance globally for debugging
                        window.notificationListenerInstance = listener;
                    } else {
                        console.warn('NotificationListener is not available on the window object');
                    }
                } catch (error) {
                    console.error('Error initializing NotificationListener:', error);
                }
                @endauth

                // Toggle notifications dropdown
                const notificationsButton = document.getElementById('notifications-menu-button');
                const notificationsDropdown = document.getElementById('notifications-dropdown');
                if (notificationsButton && notificationsDropdown) {
                    notificationsButton.addEventListener('click', () => {
                        notificationsDropdown.classList.toggle('hidden');
                    });
                }

                // Toggle user dropdown
                const userButton = document.getElementById('user-menu-button');
                const userDropdown = document.getElementById('user-dropdown');
                if (userButton && userDropdown) {
                    userButton.addEventListener('click', () => {
                        userDropdown.classList.toggle('hidden');
                    });
                }

                // Initialize sound buttons
                const soundInitContainer = document.getElementById('sound-init-container');
                const initSoundBtn = document.getElementById('init-sound-btn');
                const testSoundBtn = document.getElementById('test-sound-btn');
                
                // Initialize sound on click
                if (initSoundBtn) {
                    initSoundBtn.addEventListener('click', function() {
                        // Try to initialize audio context
                        if (window.VehicleImportNotification && window.VehicleImportNotification.initAudio) {
                            window.VehicleImportNotification.initAudio();
                        }
                        
                        // Test the sound
                        if (window.testVehicleNotificationSound) {
                            window.testVehicleNotificationSound();
                            
                            // Don't hide button to allow multiple initializations if needed
                            // soundInitContainer.style.display = 'none';
                            
                            // Add data attribute to track that it was initialized
                            initSoundBtn.setAttribute('data-initialized', 'true');
                            
                            // Change color to indicate it's been initialized
                            initSoundBtn.classList.remove('bg-primary');
                            initSoundBtn.classList.add('bg-green-500');
                        }
                    });
                }
                
                // Test sound button for admins
                if (testSoundBtn) {
                    testSoundBtn.addEventListener('click', function() {
                        if (window.testVehicleNotificationSound) {
                            window.testVehicleNotificationSound();
                        }
                    });
                }

                // Pre-load notification sound on any user interaction
                const preloadSound = function() {
                    if (window.VehicleImportNotification && window.VehicleImportNotification.initAudio) {
                        window.VehicleImportNotification.initAudio();
                        console.log('Audio context initialized via user interaction');
                    }
                    
                    // Remove this event listener after first user interaction
                    document.removeEventListener('click', preloadSound);
                    document.removeEventListener('touchstart', preloadSound);
                };
                
                // Add event listeners for first user interaction
                document.addEventListener('click', preloadSound);
                document.addEventListener('touchstart', preloadSound);

                // Close dropdowns when clicking outside
                document.addEventListener('click', (event) => {
                    if (!notificationsButton?.contains(event.target)) {
                        notificationsDropdown?.classList.add('hidden');
                    }
                    if (!userButton?.contains(event.target)) {
                        userDropdown?.classList.add('hidden');
                    }
                });
            });
        </script>
    </body>
</html>
