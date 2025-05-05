<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Offsite Vendor Tasks for Vehicle') }}
            </h2>
            <a href="{{ route('recon.offsite-inspections.index') }}" class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                Back to All Vehicles
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-full mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <!-- Vehicle Information -->
                <div class="mb-6 pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Vehicle Information</h3>
                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Stock #:</p>
                            <p class="font-medium">{{ $vehicle->stock_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">VIN:</p>
                            <p class="font-medium">{{ $vehicle->vin }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Vehicle:</p>
                            <p class="font-medium">{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status:</p>
                            @if($vehicle->status === 'warning')
                                <p class="font-medium text-yellow-500">Repair</p>
                            @elseif($vehicle->status === 'fail')
                                <p class="font-medium text-red-500">Replace</p>
                            @else
                                <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $vehicle->status)) }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Tasks List -->
                <h3 class="text-lg font-medium text-gray-900 mb-4">Offsite Vendor Tasks</h3>

                @if($offsiteItems->isEmpty())
                    <div class="text-center py-4">
                        <p class="text-gray-500">No offsite vendor tasks found for this vehicle.</p>
                    </div>
                @else
                    <!-- Table display of tasks -->
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Task
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Items
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Completion Status
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Assigned Vendor
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($offsiteItems as $item)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $item->inspectionItem->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="text-sm text-gray-900">Cost: ${{ number_format($item->cost, 2) }}</div>
                                            <div class="text-sm text-gray-500">{{ Str::limit($item->notes, 50) }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($item->status === 'in_progress')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    In Progress
                                                </span>
                                            @elseif($item->status === 'completed')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Completed
                                                </span>
                                            @elseif($item->status === 'warning')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Repair
                                                </span>
                                            @elseif($item->status === 'fail')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    Replace
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            @endif
                                            
                                            @if($item->repairImages->isNotEmpty())
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                    <x-heroicon-o-photo class="h-4 w-4 mr-1" />
                                                    {{ $item->repairImages->count() }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div>
                                                <span class="font-medium">{{ $item->assignedVendor->name }}</span>
                                                <div class="text-xs text-gray-500">
                                                    {{ $item->assignedVendor->type ? ($item->assignedVendor->type->is_on_site ? 'On-Site' : 'Off-Site') : 'Unknown' }}
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <button type="button" 
                                                    class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs leading-4 font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                                    onclick="toggleDetails('item-{{ $item->id }}')">
                                                <x-heroicon-o-eye class="h-4 w-4 mr-1" />
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                    
                                    <!-- Expandable details row -->
                                    <tr id="item-{{ $item->id }}" class="hidden bg-gray-50">
                                        <td colspan="5" class="px-6 py-4">
                                            <div class="space-y-4">
                                                <!-- Notes Section -->
                                                <div>
                                                    <h5 class="font-medium text-gray-700 mb-2">Notes:</h5>
                                                    <p class="text-gray-600">{{ $item->notes ?: 'No notes provided.' }}</p>
                                                </div>
                                                
                                                <!-- Images Section -->
                                                @if($item->repairImages->isNotEmpty())
                                                    <div>
                                                        <h5 class="font-medium text-gray-700 mb-2">Repair Images:</h5>
                                                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mt-2">
                                                            @foreach($item->repairImages as $image)
                                                                <div class="relative group">
                                                                    <a href="{{ Storage::url($image->image_path) }}" target="_blank" class="block">
                                                                        <img src="{{ Storage::url($image->image_path) }}" alt="Repair Image" class="w-full h-24 object-cover rounded border border-gray-200">
                                                                    </a>
                                                                    <form action="{{ route('recon.offsite-inspections.delete-image', $image) }}" method="POST" class="absolute top-1 right-1 hidden group-hover:block">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" class="bg-red-100 text-red-800 p-1 rounded-full hover:bg-red-200" onclick="return confirm('Are you sure you want to delete this image?')">
                                                                            <x-heroicon-o-trash class="h-4 w-4" />
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                <!-- Update Status Form -->
                                                @if(!$item->repair_completed)
                                                    <form action="{{ route('recon.offsite-inspections.update-status', $item) }}" method="POST" class="p-4 bg-white rounded-lg border border-gray-200">
                                                        @csrf
                                                        @method('PATCH')
                                                        <h5 class="font-medium text-gray-700 mb-2">Update Task Status:</h5>
                                                        
                                                        <div class="mt-3">
                                                            <label for="status_{{ $item->id }}" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                                                            <select id="status_{{ $item->id }}" name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                                <option value="in_progress" {{ $item->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                                <option value="completed" {{ $item->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                                <option value="cancelled" {{ $item->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                            </select>
                                                        </div>
                                                        
                                                        <div class="mt-3">
                                                            <label for="cost_{{ $item->id }}" class="block text-sm font-medium text-gray-700 mb-1">Cost ($)</label>
                                                            <input type="number" id="cost_{{ $item->id }}" name="cost" value="{{ $item->cost }}" step="0.01" min="0" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                        </div>
                                                        
                                                        <div class="mt-3">
                                                            <label for="notes_{{ $item->id }}" class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                                            <textarea id="notes_{{ $item->id }}" name="notes" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $item->notes }}</textarea>
                                                        </div>
                                                        
                                                        <div class="mt-3">
                                                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                                Update Status
                                                            </button>
                                                        </div>
                                                    </form>
                                                @endif
                                                
                                                <!-- Upload Images Form -->
                                                <form action="{{ route('recon.offsite-inspections.upload-images', $item) }}" method="POST" enctype="multipart/form-data" class="p-4 bg-white rounded-lg border border-gray-200">
                                                    @csrf
                                                    <h5 class="font-medium text-gray-700 mb-2">Upload Repair Images:</h5>
                                                    
                                                    <div class="mt-3">
                                                        <label for="images_{{ $item->id }}" class="block text-sm font-medium text-gray-700 mb-1">Images</label>
                                                        <input id="images_{{ $item->id }}" name="images[]" type="file" multiple accept="image/*" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                                        <p class="text-xs text-gray-500 mt-1">You can upload multiple images (max 2MB each).</p>
                                                    </div>
                                                    
                                                    <div class="mt-3">
                                                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                            Upload Images
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function toggleDetails(id) {
            const detailsRow = document.getElementById(id);
            if (detailsRow.classList.contains('hidden')) {
                detailsRow.classList.remove('hidden');
            } else {
                detailsRow.classList.add('hidden');
            }
        }
    </script>
</x-app-layout> 