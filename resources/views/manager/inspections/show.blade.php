<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('manager.inspections.index') }}" class="inline-flex items-center text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Inspections
                </a>
                <h2 class="text-xl font-semibold leading-tight text-zinc-800 dark:text-zinc-200">
                    Inspection Details
                </h2>
            </div>
            <div class="flex items-center gap-4">
                @if($inspection->status !== 'completed')
                    <button type="button" onclick="document.getElementById('approve-modal').classList.remove('hidden')"
                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Approve All & Complete
                    </button>
                @endif
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" type="button" class="inline-flex items-center px-4 py-2 text-sm font-medium text-zinc-700 bg-white rounded-md border border-zinc-300 hover:bg-zinc-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                        </svg>
                        Actions
                    </button>
                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5">
                        <div class="py-1">
                            <a href="#" class="block px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-100">Export Report</a>
                            <a href="#" class="block px-4 py-2 text-sm text-zinc-700 hover:bg-zinc-100">Print Details</a>
                            <button type="button" onclick="document.getElementById('delete-modal').classList.remove('hidden')"
                                    class="block w-full text-left px-4 py-2 text-sm text-red-700 hover:bg-red-100">
                                Delete Inspection
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <!-- Vehicle Information -->
            <div class="bg-white dark:bg-zinc-950 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-800 mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">Vehicle Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Make & Model</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">
                                {{ $inspection->vehicle->year }} {{ $inspection->vehicle->make }} {{ $inspection->vehicle->model }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">VIN</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $inspection->vehicle->vin }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Stock Number</dt>
                            <dd class="mt-1 text-sm text-zinc-900 dark:text-zinc-100">{{ $inspection->vehicle->stock_number }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Status</dt>
                            <dd class="mt-1">
                                @php
                                    $statusColor = match($inspection->status) {
                                        'completed' => 'green',
                                        'in_progress' => 'blue',
                                        'needs_attention' => 'red',
                                        default => 'zinc'
                                    };
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-{{ $statusColor }}-100 dark:bg-{{ $statusColor }}-900 text-{{ $statusColor }}-800 dark:text-{{ $statusColor }}-200">
                                    {{ str_replace('_', ' ', ucfirst($inspection->status)) }}
                                </span>
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inspection Items -->
            <div class="bg-white dark:bg-zinc-950 overflow-hidden shadow-sm sm:rounded-lg border border-zinc-200 dark:border-zinc-800">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-4">Inspection Items</h3>
                    
                    <div class="space-y-6">
                        @foreach($inspection->itemResults->groupBy('vendor_id') as $vendorId => $items)
                            @php
                                $vendor = App\Models\Vendor::find($vendorId);
                                $allCompleted = $items->every(fn($item) => $item->repair_completed || $item->status === 'cancelled');
                                $anyFailed = $items->contains(fn($item) => $item->status === 'fail');
                                $anyWarning = $items->contains(fn($item) => $item->status === 'warning');
                                $totalCost = $items->sum('actual_cost');
                            @endphp
                            
                            <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden">
                                <div class="bg-zinc-50 dark:bg-zinc-900 px-4 py-3 flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <h4 class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $vendor->name }}</h4>
                                        @if($allCompleted)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                Completed
                                            </span>
                                        @elseif($anyFailed)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                Failed Items
                                            </span>
                                        @elseif($anyWarning)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200">
                                                Warning
                                            </span>
                                        @endif
                                        @if($totalCost > 0)
                                            <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                Total: ${{ number_format($totalCost, 2) }}
                                            </span>
                                        @endif
                                    </div>
                                    <div class="text-sm text-zinc-500">
                                        {{ $items->count() }} items
                                    </div>
                                </div>
                                <div class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                    @foreach($items as $item)
                                        <div class="p-4">
                                            <div class="flex items-start justify-between">
                                                <div class="flex-1">
                                                    <div class="flex items-center gap-3">
                                                        <h5 class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                            {{ $item->inspectionItem->name }}
                                                        </h5>
                                                        @if($item->repair_completed)
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                                Completed
                                                            </span>
                                                        @elseif($item->status === 'cancelled')
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-800 dark:text-zinc-200">
                                                                Cancelled
                                                            </span>
                                                        @elseif($item->status === 'repair')
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-amber-100 dark:bg-amber-900 text-amber-800 dark:text-amber-200">
                                                                Needs Repair
                                                            </span>
                                                        @elseif($item->status === 'replace')
                                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                                Needs Replacement
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <p class="mt-1 text-sm text-zinc-500">
                                                        {{ $item->inspectionItem->description }}
                                                    </p>
                                                    @if($item->notes)
                                                        <div class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                                                            <strong>Notes:</strong> {{ $item->notes }}
                                                        </div>
                                                    @endif
                                                    @if($item->completion_notes)
                                                        <div class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
                                                            <strong>Completion Notes:</strong> {{ $item->completion_notes }}
                                                        </div>
                                                    @endif
                                                </div>
                                                <div class="ml-4 flex flex-col items-end gap-2">
                                                    @if($item->estimated_cost)
                                                        <div class="text-sm text-zinc-600 dark:text-zinc-400">
                                                            Est. Cost: ${{ number_format($item->estimated_cost, 2) }}
                                                        </div>
                                                    @endif
                                                    @if($item->actual_cost)
                                                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">
                                                            Final Cost: ${{ number_format($item->actual_cost, 2) }}
                                                        </div>
                                                    @endif
                                                    @if($item->requires_repair)
                                                        <div class="text-sm text-amber-600 dark:text-amber-400">
                                                            Repair Required
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($item->photos->isNotEmpty())
                                                <div class="mt-4 grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                                                    @foreach($item->photos as $photo)
                                                        <a href="{{ Storage::url($photo->path) }}" target="_blank" class="block aspect-w-16 aspect-h-9 rounded-lg overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                                                            <img src="{{ Storage::url($photo->path) }}" alt="Inspection photo" class="object-cover">
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div id="approve-modal" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-zinc-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-zinc-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-zinc-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 dark:bg-green-900 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-zinc-900 dark:text-zinc-100" id="modal-title">
                                Complete Inspection
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                    Are you sure you want to approve all items and mark this inspection as complete? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form action="{{ route('manager.inspections.complete', $inspection) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Complete Inspection
                        </button>
                    </form>
                    <button type="button" onclick="document.getElementById('approve-modal').classList.add('hidden')"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-zinc-300 dark:border-zinc-600 shadow-sm px-4 py-2 bg-white dark:bg-zinc-900 text-base font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="delete-modal" class="hidden fixed z-10 inset-0 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-zinc-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white dark:bg-zinc-900 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white dark:bg-zinc-900 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600 dark:text-red-200" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-zinc-900 dark:text-zinc-100" id="modal-title">
                                Delete Inspection
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                    Are you sure you want to delete this inspection? All data will be permanently removed. This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-zinc-50 dark:bg-zinc-800 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <form action="{{ route('manager.inspections.destroy', $inspection) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Delete
                        </button>
                    </form>
                    <button type="button" onclick="document.getElementById('delete-modal').classList.add('hidden')"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-zinc-300 dark:border-zinc-600 shadow-sm px-4 py-2 bg-white dark:bg-zinc-900 text-base font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-50 dark:hover:bg-zinc-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-zinc-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 