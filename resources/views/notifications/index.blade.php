<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                {{ __('Notifications') }}
            </h2>
            @if($notifications->total() > 0)
                <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="flex items-center">
                    @csrf
                    <button type="submit" class="text-sm text-primary hover:text-primary-dark">
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if($notifications->total() > 0)
                        <div class="space-y-4">
                            @foreach($notifications as $notification)
                                <div class="flex items-start justify-between p-4 {{ $notification->read_at ? 'bg-gray-50' : 'bg-white' }} rounded-lg border">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $notification->data['message'] ?? 'Notification' }}
                                            </p>
                                            <span class="text-xs text-gray-500">
                                                {{ $notification->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        @if(isset($notification->data['description']))
                                            <p class="mt-1 text-sm text-gray-500">
                                                {{ $notification->data['description'] }}
                                            </p>
                                        @endif
                                        <div class="mt-2 flex items-center gap-4">
                                            @if(isset($notification->data['url']))
                                                <a href="{{ $notification->data['url'] }}" class="text-sm text-primary hover:text-primary-dark">
                                                    View details
                                                </a>
                                            @endif
                                            @if(!$notification->read_at)
                                                <form action="{{ route('notifications.mark-as-read', $notification->id) }}" method="POST" class="flex items-center">
                                                    @csrf
                                                    <button type="submit" class="text-sm text-gray-500 hover:text-gray-700">
                                                        Mark as read
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6">
                            {{ $notifications->links() }}
                        </div>
                    @else
                        <div class="text-center">
                            <x-heroicon-o-bell class="mx-auto h-12 w-12 text-gray-400" />
                            <h3 class="mt-2 text-sm font-semibold text-gray-900">No notifications</h3>
                            <p class="mt-1 text-sm text-gray-500">You don't have any notifications yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 