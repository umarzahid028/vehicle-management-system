@props([
    'type' => 'button',
    'variant' => 'default', 
    'size' => 'default',
    'class' => '',
])

@php
    $baseClass = 'inline-flex items-center justify-center rounded-md font-medium transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none';
    
    $variants = [
        'default' => 'bg-primary text-primary-foreground hover:bg-primary/90',
        'destructive' => 'bg-destructive text-destructive-foreground hover:bg-destructive/90',
        'outline' => 'border border-input hover:bg-accent hover:text-accent-foreground',
        'secondary' => 'bg-secondary text-secondary-foreground hover:bg-secondary/80',
        'ghost' => 'hover:bg-accent hover:text-accent-foreground',
        'link' => 'underline-offset-4 hover:underline text-primary',
    ];
    
    $sizes = [
        'default' => 'h-10 py-2 px-4 text-sm',
        'sm' => 'h-9 px-3 text-xs',
        'lg' => 'h-11 px-8 text-base',
        'icon' => 'h-10 w-10',
    ];
    
    $variantClass = $variants[$variant] ?? $variants['default'];
    $sizeClass = $sizes[$size] ?? $sizes['default'];
    
    $classes = "{$baseClass} {$variantClass} {$sizeClass} {$class}";
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button> 