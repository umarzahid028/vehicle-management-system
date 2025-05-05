@props(['href' => '#'])

<a {{ $attributes->merge(['class' => 'block w-full px-4 py-2 text-sm leading-5 text-foreground transition duration-150 ease-in-out hover:bg-accent focus:outline-none focus:bg-accent']) }} href="{{ $href }}">
    {{ $slot }}
</a>
