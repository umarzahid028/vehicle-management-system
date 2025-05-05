<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
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
    </head>
    <body class="h-full bg-gray-50 font-sans antialiased">
        <div class="min-h-screen flex flex-col items-center justify-center p-4 sm:p-6">
            <div class="w-full max-w-md">
                <div class="bg-white border border-gray-200 rounded-xl shadow-md p-8">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
