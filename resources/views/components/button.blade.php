@props(['href' => null, 'variant' => 'primary', 'type' => 'button'])

@php
    $baseClasses = 'inline-flex items-center px-4 py-2 border rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150';
    
    $variantClasses = [
        'primary' => 'bg-gray-800 border-transparent hover:bg-gray-700 active:bg-gray-900 focus:border-gray-900 focus:ring-gray-300',
        'secondary' => 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50 active:bg-gray-100 focus:border-indigo-500 focus:ring-indigo-500',
        'danger' => 'bg-red-600 border-transparent hover:bg-red-500 active:bg-red-700 focus:border-red-700 focus:ring-red-300',
        'success' => 'bg-green-600 border-transparent hover:bg-green-500 active:bg-green-700 focus:border-green-700 focus:ring-green-300',
        'outline' => 'bg-transparent border-gray-300 text-gray-700 hover:bg-gray-50 active:bg-gray-100 focus:border-indigo-500 focus:ring-indigo-500',
    ];

    $classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif