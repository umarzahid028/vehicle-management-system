@props([
    'variant' => 'default'
])

@php
$variants = [
    'default' => 'bg-primary/10 text-primary border-primary/20',
    'success' => 'bg-green-50 text-green-700 border-green-200',
    'warning' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
    'danger' => 'bg-red-50 text-red-700 border-red-200',
    'info' => 'bg-blue-50 text-blue-700 border-blue-200',
    'gray' => 'bg-gray-50 text-gray-700 border-gray-200',
];

$variantClasses = $variants[$variant] ?? $variants['default'];
$classes = 'inline-flex items-center rounded-md border px-2 py-1 text-xs font-medium ' . $variantClasses;
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span> 