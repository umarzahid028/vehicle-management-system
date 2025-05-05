<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inspection Items') }}
            </h2>
            <a href="{{ route('inspection.items.create') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <x-heroicon-o-plus class="h-4 w-4 mr-1" />
                Add Item
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
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

                    <!-- Filter Bar -->
                    <div class="mb-6 flex flex-col md:flex-row gap-4 items-start md:items-center">
                        <div>
                            <label for="stage-filter" class="block text-sm font-medium text-gray-700 mb-1">Filter by Stage:</label>
                            <select id="stage-filter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                onchange="window.location.href='{{ route('inspection.items.index') }}' + (this.value ? '?stage_id=' + this.value : '')">
                                <option value="">All Stages</option>
                                @foreach($stages as $filterStage)
                                    @if(is_object($filterStage))
                                        <option value="{{ $filterStage->id }}" {{ isset($stageId) && $stageId == $filterStage->id ? 'selected' : '' }}>
                                            {{ $filterStage->name }}
                                        </option>
                                    @elseif(is_array($filterStage) && isset($filterStage['id']))
                                        <option value="{{ $filterStage['id'] }}" {{ isset($stageId) && $stageId == $filterStage['id'] ? 'selected' : '' }}>
                                            {{ $filterStage['name'] ?? 'Unknown Stage' }}
                                        </option>
                                    @else
                                        <option value="{{ $filterStage }}" {{ isset($stageId) && $stageId == $filterStage ? 'selected' : '' }}>
                                            {{ $filterStage }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="status-filter" class="block text-sm font-medium text-gray-700 mb-1">Status:</label>
                            <select id="status-filter" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                onchange="window.location.href='{{ route('inspection.items.index') }}' + 
                                    (document.getElementById('stage-filter').value ? '?stage_id=' + document.getElementById('stage-filter').value : '') + 
                                    (this.value ? (document.getElementById('stage-filter').value ? '&' : '?') + 'status=' + this.value : '')">
                                <option value="">All Status</option>
                                <option value="active" {{ isset($status) && $status == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ isset($status) && $status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Inspection Items Table -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stage</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($items as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $item->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <a href="{{ route('inspection.stages.show', $item->inspectionStage) }}" class="text-indigo-600 hover:text-indigo-900">
                                                    {{ $item->inspectionStage->name }}
                                                </a>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-500 max-w-xs truncate">
                                                {{ $item->description ?? 'No description' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                {{ $item->is_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex space-x-2">
                                                <a href="{{ route('inspection.items.edit', $item) }}" class="text-indigo-600 hover:text-indigo-900" title="Edit">
                                                    <x-heroicon-o-pencil class="h-5 w-5" />
                                                </a>
                                                
                                                <form method="POST" action="{{ route('inspection.items.toggle-active', $item) }}" class="inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="{{ $item->is_active ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}" title="{{ $item->is_active ? 'Deactivate' : 'Activate' }}">
                                                        @if($item->is_active)
                                                            <x-heroicon-o-x-circle class="h-5 w-5" />
                                                        @else
                                                            <x-heroicon-o-check-circle class="h-5 w-5" />
                                                        @endif
                                                    </button>
                                                </form>
                                                
                                                <form method="POST" action="{{ route('inspection.items.destroy', $item) }}" class="inline delete-form" data-name="{{ $item->name }}">
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
                                            No inspection items found.
                                            @if($stageId)
                                                <a href="{{ route('inspection.items.create', ['stage_id' => $stageId]) }}" class="text-indigo-600 hover:text-indigo-900">Create an item for this stage</a>.
                                            @else
                                                <a href="{{ route('inspection.items.create') }}" class="text-indigo-600 hover:text-indigo-900">Create the first one</a>.
                                            @endif
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteForms = document.querySelectorAll('.delete-form');
            deleteForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const itemName = this.getAttribute('data-name');
                    
                    if (confirm(`Are you sure you want to delete the item "${itemName}"? This action cannot be undone.`)) {
                        this.submit();
                    }
                });
            });
        });
    </script>
    @endpush
</x-app-layout> 