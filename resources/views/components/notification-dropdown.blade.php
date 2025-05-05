@props(['align' => 'right', 'width' => '48', 'contentClasses' => 'py-1 bg-white'])

@php
switch ($align) {
    case 'left':
        $alignmentClasses = 'origin-top-left left-0';
        break;
    case 'top':
        $alignmentClasses = 'origin-top';
        break;
    case 'right':
    default:
        $alignmentClasses = 'origin-top-right right-0';
        break;
}

switch ($width) {
    case '48':
        $width = 'w-48';
        break;
    case '96':
        $width = 'w-96';
        break;
}
@endphp

<div class="relative" x-data="{ open: false }" @click.away="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="transform opacity-0 scale-95"
            x-transition:enter-end="transform opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="transform opacity-100 scale-100"
            x-transition:leave-end="transform opacity-0 scale-95"
            class="absolute z-50 mt-2 {{ $width }} rounded-md shadow-lg {{ $alignmentClasses }}"
            style="display: none;"
            @click="open = false">
        <div class="rounded-md ring-1 ring-black ring-opacity-5 {{ $contentClasses }}">
            <div class="p-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">Notifications</h3>
                    @if(auth()->user()->unreadNotifications->count() > 0)
                        <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                            @csrf
                            <button type="submit" class="text-sm text-primary hover:underline">
                                Mark all as read
                            </button>
                        </form>
                    @endif
                </div>

                <div class="space-y-4 max-h-[400px] overflow-y-auto">
                    @forelse(auth()->user()->notifications()->take(5)->get() as $notification)
                        <div @class([
                            'flex items-start space-x-4 p-3 rounded-lg transition-colors',
                            'bg-muted/50' => !$notification->read_at,
                            'hover:bg-muted/30' => true
                        ])>
                            <div class="flex-shrink-0">
                                @if($notification->type === 'App\Notifications\NewTransportAssigned')
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary/10">
                                        <x-heroicon-o-truck class="w-4 h-4 text-primary"/>
                                    </span>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between">
                                    <p class="text-sm font-medium text-foreground">
                                        {{ $notification->data['message'] }}
                                    </p>
                                    <p class="ml-2 text-xs text-muted-foreground whitespace-nowrap">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </p>
                                </div>
                                <p class="mt-1 text-sm text-muted-foreground">
                                    {{ $notification->data['vehicle'] }}
                                </p>
                                <p class="mt-1 text-xs text-muted-foreground">
                                    {{ $notification->data['origin'] }} → {{ $notification->data['destination'] }}
                                </p>
                                <div class="mt-2">
                                    <a href="{{ $notification->data['link'] }}" 
                                       class="text-sm font-medium text-primary hover:underline">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-muted mb-4">
                                <x-heroicon-o-bell class="w-6 h-6 text-muted-foreground"/>
                            </div>
                            <p class="text-sm text-muted-foreground">No notifications</p>
                        </div>
                    @endforelse

                    @if(auth()->user()->notifications->count() > 5)
                        <div class="text-center pt-2">
                            <a href="{{ route('notifications.index') }}" class="text-sm text-primary hover:underline">
                                View all notifications
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div> 