<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inspection Stages') }}
            </h2>
            <a href="{{ route('inspection.stages.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <x-heroicon-o-plus class="h-4 w-4 mr-1" />
                Add Stage
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6 py-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                            <p>{{ session('success') }}</p>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                            <p>{{ session('error') }}</p>
                        </div>
                    @endif

                    <div class="overflow-x-auto" x-data="{ reordering: false }">
                        <div class="mb-4 flex justify-end">
                            <button 
                                @click="reordering = !reordering" 
                                x-text="reordering ? 'Save Order' : 'Reorder Stages'"
                                :class="reordering ? 'bg-green-600 hover:bg-green-700' : 'bg-indigo-600 hover:bg-indigo-700'"
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150"
                                @click="if(reordering) saveOrder()"
                            ></button>
                        </div>
                        
                        <table class="min-w-full divide-y divide-gray-200" id="stages-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="sortable-stages">
                                @forelse($stages as $stage)
                                    <tr class="stage-row" data-id="{{ $stage->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span x-show="!reordering" class="text-sm text-gray-900">{{ $stage->order }}</span>
                                            <span x-show="reordering" class="text-sm text-gray-900 cursor-move px-2 py-1 bg-gray-100 rounded">
                                                <x-heroicon-o-arrows-up-down class="h-4 w-4 inline" />
                                                {{ $stage->order }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                <a href="{{ route('inspection.stages.show', $stage) }}" class="hover:text-indigo-600 hover:underline">
                                                    {{ $stage->name }}
                                                </a>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-500 max-w-xs truncate">
                                                {{ $stage->description ?? 'No description' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $stage->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $stage->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('inspection.stages.edit', $stage) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                    <x-heroicon-o-pencil class="h-5 w-5" />
                                                </a>
                                                
                                                <form method="POST" action="{{ route('inspection.stages.toggle-active', $stage) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="{{ $stage->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}" title="{{ $stage->is_active ? 'Deactivate' : 'Activate' }}">
                                                        @if($stage->is_active)
                                                            <x-heroicon-o-x-circle class="h-5 w-5" />
                                                        @else
                                                            <x-heroicon-o-check-circle class="h-5 w-5" />
                                                        @endif
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" action="{{ route('inspection.stages.destroy', $stage) }}" class="inline delete-form" data-name="{{ $stage->name }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                        <x-heroicon-o-trash class="h-5 w-5" />
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                            No inspection stages found. <a href="{{ route('inspection.stages.create') }}" class="text-indigo-600 hover:text-indigo-900">Create the first one</a>.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize sortable
            const sortableList = document.getElementById('sortable-stages');
            if (sortableList) {
                const sortable = new Sortable(sortableList, {
                    animation: 150,
                    handle: '.cursor-move',
                    ghostClass: 'bg-gray-100',
                    onEnd: function() {
                        // This will be triggered when drag ends
                    }
                });
            }

            // Delete confirmation
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const stageName = this.getAttribute('data-name');
                    
                    if (confirm(`Are you sure you want to delete the stage "${stageName}"? This action cannot be undone.`)) {
                        this.submit();
                    }
                });
            });

            // Save stage order
            window.saveOrder = function() {
                const rows = document.querySelectorAll('.stage-row');
                const stages = Array.from(rows).map((row, index) => {
                    return row.getAttribute('data-id');
                });
                
                fetch('{{ route("inspection.stages.reorder") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ stages })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error saving order:', error);
                    alert('There was an error saving the order. Please try again.');
                });
            };
        });
    </script>
    @endpush
</x-app-layout> 