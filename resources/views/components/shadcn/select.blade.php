@props([
    'name' => '',
    'id' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'class' => '',
])

@php
    $id = $id ?? $name;
    $baseClass = 'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50';
    $classes = "{$baseClass} {$class}";
@endphp

<select 
    name="{{ $name }}" 
    id="{{ $id }}" 
    @if($required) required @endif
    @if($disabled) disabled @endif
    {{ $attributes->merge(['class' => $classes]) }}
>
    @if($placeholder)
        <option value="">{{ $placeholder }}</option>
    @endif
    
    {{ $slot }}
</select> 