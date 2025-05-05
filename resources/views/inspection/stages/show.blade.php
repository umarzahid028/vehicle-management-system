<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inspection Stage') }}: {{ $stage->name }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('inspection.stages.edit', $stage) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <x-heroicon-o-pencil class="h-4 w-4 mr-1" />
                    Edit
                </a>
                <a href="{{ route('inspection.stages.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                    Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6 py-6">
            <!-- Stage Details Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Stage Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <span class="text-gray-500 text-sm">Name:</span>
                            <p class="font-medium">{{ $stage->name }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 text-sm">Order:</span>
                            <p class="font-medium">{{ $stage->order }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500 text-sm">Status:</span>
                            <p class="font-medium">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $stage->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $stage->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </p>
                        </div>
                        
                        @if($stage->description)
                        <div class="md:col-span-3 mt-2">
                            <span class="text-gray-500 text-sm">Description:</span>
                            <p class="text-sm text-gray-900">{{ $stage->description }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Inspection Items -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Inspection Items in this Stage</h3>
                        
                        <a href="{{ route('inspection.items.create', ['stage_id' => $stage->id]) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <x-heroicon-o-plus class="h-4 w-4 mr-1" />
                            Add Item
                        </a>
                    </div>
                    
                    @if($items->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($items as $item)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ $item->name }}</div>
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
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-md p-4 text-center text-gray-500">
                            No inspection items in this stage yet. 
                            <a href="{{ route('inspection.items.create', ['stage_id' => $stage->id]) }}" class="text-indigo-600 hover:text-indigo-900">Add the first one</a>.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 