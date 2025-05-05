@props(['title' => null, 'description' => null])

<div {{ $attributes->merge(['class' => 'rounded-lg border bg-card text-card-foreground shadow']) }}>
    @if($title || $description)
    <div class="p-6 flex flex-col space-y-1.5">
        @if($title)
        <h3 class="font-semibold leading-none tracking-tight text-lg">{{ $title }}</h3>
        @endif
        
        @if($description)
        <p class="text-sm text-muted-foreground">{{ $description }}</p>
        @endif
    </div>
    @endif
    
    <div class="p-6 pt-0">
        {{ $slot }}
    </div>
</div> 