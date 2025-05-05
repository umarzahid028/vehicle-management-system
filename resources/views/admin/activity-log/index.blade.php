<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <h2 class="text-2xl font-semibold tracking-tight">Activity Log</h2>
                <p class="text-sm text-muted-foreground">View system activity and audit logs.</p>
            </div>
        </div>
    </x-slot>

    <div class="container mx-auto space-y-6">
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm mx-4 sm:mx-6 lg:mx-8">
            <div class="relative w-full overflow-auto">
                <table class="w-full caption-bottom text-sm">
                    <thead class="[&_tr]:border-b">
                        <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Date</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">User</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Action</th>
                            <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground">Details</th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:border-0">
                        @forelse($activities as $activity)
                            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                <td class="p-4 align-middle">
                                    <div class="flex flex-col">
                                        <span class="font-medium">{{ $activity->created_at->format('M d, Y') }}</span>
                                        <span class="text-xs text-muted-foreground">{{ $activity->created_at->format('h:i A') }}</span>
                                    </div>
                                </td>
                                <td class="p-4 align-middle">
                                    @if($activity->causer)
                                        <div class="flex items-center gap-2">
                                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-full">
                                                <span class="flex h-full w-full items-center justify-center rounded-full bg-primary/10 text-primary">
                                                    {{ strtoupper(substr($activity->causer->name, 0, 1)) }}
                                                </span>
                                            </span>
                                            <div>
                                                <p class="text-sm font-medium leading-none">{{ $activity->causer->name }}</p>
                                                <p class="text-xs text-muted-foreground">{{ $activity->causer->email }}</p>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted-foreground">System</span>
                                    @endif
                                </td>
                                <td class="p-4 align-middle">
                                    <span class="inline-flex items-center rounded-md bg-primary/10 px-2 py-1 text-xs font-medium text-primary ring-1 ring-inset ring-primary/20">
                                        {{ $activity->description }}
                                    </span>
                                </td>
                                <td class="p-4 align-middle">
                                    @if($activity->properties->count() > 0)
                                        <div class="text-sm">
                                            @foreach($activity->properties as $key => $value)
                                                @if($key !== 'attributes' && $key !== 'old')
                                                    <div class="flex items-start gap-2">
                                                        <span class="font-medium">{{ Str::title(str_replace('_', ' ', $key)) }}:</span>
                                                        <span class="text-muted-foreground">
                                                            @if(is_array($value))
                                                                {{ json_encode($value) }}
                                                            @else
                                                                {{ $value }}
                                                            @endif
                                                        </span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-4 text-center">
                                    <div class="flex flex-col items-center justify-center py-8">
                                        <x-heroicon-o-clock class="h-12 w-12 text-muted-foreground/60" />
                                        <h3 class="mt-2 text-lg font-medium">No activities found</h3>
                                        <p class="text-sm text-muted-foreground">System activities will appear here.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($activities->hasPages())
            <div class="px-4 sm:px-6 lg:px-8">
                {{ $activities->links() }}
            </div>
        @endif
    </div>
</x-app-layout> 