<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inspection Details') }}
            </h2>
            <a href="{{ route('vendor.inspections.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <!-- Vehicle Information -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Vehicle Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-gray-600">Vehicle</p>
                            <p class="text-base font-medium">{{ $inspection->vehicle->year }} {{ $inspection->vehicle->make }} {{ $inspection->vehicle->model }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">VIN</p>
                            <p class="text-base font-medium">{{ $inspection->vehicle->vin }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Stock Number</p>
                            <p class="text-base font-medium">{{ $inspection->vehicle->stock_number }}</p>
                        </div>
                        <div>
                            <p class="text-sm text-gray-600">Status</p>
                            <p class="text-base font-medium">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $inspection->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ ucfirst($inspection->status) }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inspection Items -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Assigned Items</h3>
                    
                    @if($inspection->itemResults->isEmpty())
                        <p class="text-gray-500">No items assigned to you for this inspection.</p>
                    @else
                        <div class="space-y-6">
                            @foreach($inspection->itemResults as $item)
                                <div class="border rounded-lg p-4 {{ $item->completed_at ? 'bg-gray-50' : 'bg-white' }}">
                                    <div class="flex flex-col md:flex-row md:items-start md:justify-between">
                                        <div class="flex-grow">
                                            <h4 class="text-base font-medium text-gray-900">{{ $item->inspectionItem->name }}</h4>
                                            @if($item->inspectionItem->description)
                                                <p class="mt-1 text-sm text-gray-500">{{ $item->inspectionItem->description }}</p>
                                            @endif
                                            
                                            @if($item->notes)
                                                <div class="mt-2">
                                                    <p class="text-sm text-gray-600">Notes:</p>
                                                    <p class="text-sm text-gray-900">{{ $item->notes }}</p>
                                                </div>
                                            @endif

                                            @if($item->actual_cost)
                                                <div class="mt-2">
                                                    <p class="text-sm text-gray-600">Actual Cost:</p>
                                                    <p class="text-sm text-gray-900">${{ number_format($item->actual_cost, 2) }}</p>
                                                </div>
                                            @endif

                                            @if($item->completion_notes)
                                                <div class="mt-2">
                                                    <p class="text-sm text-gray-600">Completion Notes:</p>
                                                    <p class="text-sm text-gray-900">{{ $item->completion_notes }}</p>
                                                </div>
                                            @endif

                                            <!-- Image Section -->
                                            <div class="mt-4">
                                                <div class="space-y-4">
                                                    <!-- Before Images Display -->
                                                    @if($item->repairImages->where('image_type', 'before')->count() > 0)
                                                        <div class="mb-6">
                                                            <h4 class="text-sm font-medium text-gray-900 mb-2">Pre-Repair Images</h4>
                                                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                                                @foreach($item->repairImages->where('image_type', 'before') as $image)
                                                                    <div class="relative group">
                                                                        <img src="{{ Storage::url($image->image_path) }}" 
                                                                             alt="Pre-repair Image" 
                                                                             class="w-full h-32 object-cover rounded-lg">
                                                                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-lg">
                                                                            <a href="{{ Storage::url($image->image_path) }}" 
                                                                               target="_blank"
                                                                               class="text-white hover:text-blue-500 transition-colors">
                                                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                                </svg>
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- After/Documentation Images Display -->
                                                    @if($item->repairImages->whereIn('image_type', ['after', 'documentation'])->count() > 0)
                                                        <div class="mb-6">
                                                            <h4 class="text-sm font-medium text-gray-900 mb-2">After/Documentation Images</h4>
                                                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                                                @foreach($item->repairImages->whereIn('image_type', ['after', 'documentation']) as $image)
                                                                    <div class="relative group">
                                                                        <img src="{{ Storage::url($image->image_path) }}" 
                                                                             alt="{{ ucfirst($image->image_type) }} Image" 
                                                                             class="w-full h-32 object-cover rounded-lg">
                                                                        <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-lg">
                                                                            <div class="flex space-x-2">
                                                                                <a href="{{ Storage::url($image->image_path) }}" 
                                                                                   target="_blank"
                                                                                   class="text-white hover:text-blue-500 transition-colors">
                                                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                                                    </svg>
                                                                                </a>
                                                                                <form action="{{ route('repair-images.destroy', $image) }}" method="POST" class="inline">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit" 
                                                                                            class="text-white hover:text-red-500 transition-colors"
                                                                                            onclick="return confirm('Are you sure you want to delete this image?')">
                                                                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                                        </svg>
                                                                                    </button>
                                                                                </form>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif

                                                    <!-- Upload Form - Only show when work is in progress -->
                                                    @if(!$item->completed_at)
                                                        <form action="{{ route('vendor.inspections.upload-images', $item) }}" 
                                                              method="POST" 
                                                              enctype="multipart/form-data" 
                                                              class="space-y-4">
                                                            @csrf
                                                            <div>
                                                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                                                    Upload Work Progress/Completion Images
                                                                </label>
                                                                <div class="flex flex-col space-y-4">
                                                                    <div class="flex-grow">
                                                                        <label class="flex flex-col items-center px-4 py-6 bg-white border border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50 transition-colors">
                                                                            <div class="flex flex-col items-center">
                                                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                                                </svg>
                                                                                <span class="mt-2 text-sm text-gray-600">Click to upload images</span>
                                                                                <span class="mt-1 text-xs text-gray-500">(Max 5MB per image)</span>
                                                                            </div>
                                                                            <input type="file" 
                                                                                   name="images[]" 
                                                                                   multiple 
                                                                                   accept="image/jpeg,image/png,image/jpg" 
                                                                                   class="hidden"
                                                                                   onchange="showFileNames(this)">
                                                                        </label>
                                                                        <div id="fileNames-{{ $item->id }}" class="mt-2 text-sm text-gray-600"></div>
                                                                    </div>
                                                                    <div class="flex space-x-4">
                                                                        <div class="w-1/2">
                                                                            <label class="block text-sm font-medium text-gray-700 mb-1">Image Type</label>
                                                                            <select name="image_type" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                                                                <option value="after">After Repair</option>
                                                                                <option value="documentation">Documentation</option>
                                                                            </select>
                                                                        </div>
                                                                        <div class="w-1/2">
                                                                            <label class="block text-sm font-medium text-gray-700 mb-1">Caption (Optional)</label>
                                                                            <input type="text" 
                                                                                   name="caption" 
                                                                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                                                                   placeholder="Brief description of the image">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="flex justify-end">
                                                                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                                    Upload Images
                                                                </button>
                                                            </div>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4 md:mt-0 md:ml-6">
                                            @if($item->completed_at)
                                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $item->status === 'completed' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                                    {{ ucfirst($item->status) }}
                                                </span>
                                            @else
                                                <form action="{{ route('vendor.inspections.update-item', $item) }}" method="POST" class="flex flex-col space-y-2">
                                                    @csrf
                                                    @method('PATCH')
                                                    
                                                    <div class="flex flex-col space-y-2">
                                                        <div class="flex space-x-2">
                                                            <button type="submit" name="status" value="completed" class="inline-flex items-center px-3 py-1 border border-blue-600 rounded-md text-sm font-medium text-blue-600 hover:bg-blue-50">
                                                                <x-heroicon-o-wrench class="h-4 w-4 mr-1" />
                                                                Repair/Replace
                                                            </button>
                                                            <button type="submit" name="status" value="cancelled" class="inline-flex items-center px-3 py-1 border border-red-600 rounded-md text-sm font-medium text-red-600 hover:bg-red-50">
                                                                <x-heroicon-o-x-mark class="h-4 w-4 mr-1" />
                                                                Cancel
                                                            </button>
                                                        </div>
                                                        
                                                        <input type="number" name="actual_cost" step="0.01" min="0" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Actual cost" required>
                                                        
                                                        <textarea name="completion_notes" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm text-sm" placeholder="Add completion notes"></textarea>
                                                    </div>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
function showFileNames(input) {
    const filesDiv = document.getElementById('fileNames-' + input.closest('form').querySelector('[name="images[]"]').closest('.space-y-4').closest('.mt-4').closest('.flex-grow').closest('.flex-col').closest('.border').querySelector('h4').textContent.trim());
    filesDiv.innerHTML = '';
    
    if (input.files.length > 0) {
        const fileList = document.createElement('ul');
        fileList.className = 'list-disc pl-5';
        
        Array.from(input.files).forEach(file => {
            const li = document.createElement('li');
            li.textContent = file.name;
            fileList.appendChild(li);
        });
        
        filesDiv.appendChild(fileList);
    }
}
</script>
@endpush 