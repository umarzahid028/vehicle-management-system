@props([
    'type' => 'text',
    'name' => '',
    'id' => null,
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'autofocus' => false,
    'disabled' => false,
    'class' => '',
])

@php
    $id = $id ?? $name;
    $baseClass = 'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50';
    $classes = "{$baseClass} {$class}";
@endphp

<input 
    type="{{ $type }}" 
    name="{{ $name }}" 
    id="{{ $id }}" 
    value="{{ $value }}" 
    placeholder="{{ $placeholder }}" 
    @if($required) required @endif
    @if($autofocus) autofocus @endif
    @if($disabled) disabled @endif
    {{ $attributes->merge(['class' => $classes]) }}
/> 