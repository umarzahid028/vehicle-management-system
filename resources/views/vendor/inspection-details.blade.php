<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inspection Details') }}
            </h2>
            <a href="{{ url()->previous() }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <!-- Vehicle Info -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Vehicle Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">{{ $inspection->vehicle->year }} {{ $inspection->vehicle->make }} {{ $inspection->vehicle->model }}</p>
                            <p class="text-gray-600">VIN: {{ $inspection->vehicle->vin }}</p>
                            <p class="text-gray-600">Stock #: {{ $inspection->vehicle->stock_number }}</p>
                        </div>
                        <div>
                            <p class="text-gray-600">
                                <span class="font-medium">Inspection Created:</span> 
                                {{ $inspection->created_at->format('M d, Y h:ia') }}
                            </p>
                            @if($inspection->completed_at)
                                <p class="text-gray-600">
                                    <span class="font-medium">Completed:</span>
                                    {{ $inspection->completed_at->format('M d, Y h:ia') }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inspection Items -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Assigned Items</h3>
                    
                    @if($inspection->inspectionItems->isEmpty())
                        <p class="text-gray-500">No items assigned to you for this inspection.</p>
                    @else
                        <div class="space-y-6">
                            @foreach($inspection->inspectionItems as $item)
                            @if($item->status !== 'pass' and $item->vendor_id === Auth::user()->vendor->id)
                        
                                <div id="item-{{ $item->id }}" class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0">
                                    <div class="flex flex-col md:flex-row md:items-start md:justify-between">
                                        <div class="flex-grow">
                                            <h4 class="text-base font-medium text-gray-900">{{ $item->inspectionItem->name }}</h4>
                                            @if($item->inspectionItem->description)
                                                <p class="mt-1 text-sm text-gray-500">{{ $item->inspectionItem->description }}</p>
                                            @endif
                                            @if($item->notes)
                                                <div class="mt-2">
                                                    <span class="text-sm font-medium text-gray-500">Inspector Notes:</span>
                                                    <p class="mt-1 text-sm text-gray-600">{{ $item->notes }}</p>
                                                </div>
                                            @endif
                                        </div>
                                      
                                        <div class="mt-2 md:mt-0 md:ml-4">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $item->getStatusBadgeClasses() }}">
                                                {{ $item->getStatusLabel() }}
                                            </span>
                                        </div>
                                    </div>

                                    @if($item->status === 'warning' || $item->status === 'fail')
                                        <div class="mt-4">
                                            <!-- Start Work Button -->
                                            <form action="{{ route('vendor.inspections.update-item', $item) }}#item-{{ $item->id }}" method="POST" class="mb-4">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="in_progress">
                                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    Start Work
                                                </button>
                                            </form>
                                        </div>
                                    @elseif(in_array($item->status, ['in_progress', 'diagnostic']))
                                        <div class="mt-4 space-y-6">
                                            <!-- Completion Form -->
                                            <form action="{{ route('vendor.inspections.update-item', $item) }}#item-{{ $item->id }}" method="POST" class="space-y-4" enctype="multipart/form-data">
                                                @csrf
                                                @method('PATCH')
                                                
                                                <!-- Actual Cost Field -->
                                                <div>
                                                    <label for="actual_cost_{{ $item->id }}" class="block text-sm font-medium text-gray-700">
                                                        Actual Cost <span class="text-red-500">*</span>
                                                    </label>
                                                    <div class="mt-1 relative rounded-md shadow-sm">
                                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                            <span class="text-gray-500 sm:text-sm">$</span>
                                                        </div>
                                                        <input type="number" step="0.01" min="0" name="actual_cost" id="actual_cost_{{ $item->id }}"
                                                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md"
                                                            placeholder="0.00" required>
                                                    </div>
                                                </div>
                                                
                                                <!-- Completion Notes -->
                                                <div>
                                                    <label for="completion_notes_{{ $item->id }}" class="block text-sm font-medium text-gray-700">
                                                        Completion Notes <span class="text-red-500">*</span>
                                                    </label>
                                                    <div class="mt-1">
                                                        <textarea id="completion_notes_{{ $item->id }}" name="completion_notes" rows="3"
                                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                                            placeholder="Enter detailed notes about the work performed..." required></textarea>
                                                    </div>
                                                </div>
                                                
                                                <!-- Submit Buttons -->
                                                <div class="flex space-x-3">
                                                    <button type="submit" name="status" value="completed" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                                        Mark as Completed
                                                    </button>
                                                    
                                                    <button type="submit" name="status" value="cancelled" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                        Cancel Work
                                                    </button>
                                                </div>
                                            </form>
                                            
                                            <!-- Display uploaded images -->
                                            @if($item->repairImages->count() > 0)
                                                <div class="border-t border-gray-200 pt-4 mt-4">
                                                    <h5 class="text-sm font-medium text-gray-700 mb-2">Uploaded Photos</h5>
                                                    
                                                    <!-- Before Images -->
                                                    @if($item->repairImages->where('image_type', 'before')->count() > 0)
                                                        <div class="mb-4">
                                                            <h6 class="text-xs font-medium text-gray-500 mb-2">Before Repair</h6>
                                                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                                                                @foreach($item->repairImages->where('image_type', 'before') as $image)
                                                                    <a href="{{ Storage::url($image->image_path) }}" target="_blank" class="block">
                                                                        <img src="{{ Storage::url($image->image_path) }}" alt="Before repair" class="object-cover h-24 w-full rounded-lg">
                                                                        @if($image->caption)
                                                                            <p class="text-xs text-gray-500 mt-1 truncate">{{ $image->caption }}</p>
                                                                        @endif
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- After Images -->
                                                    @if($item->repairImages->where('image_type', 'after')->count() > 0)
                                                        <div class="mb-4">
                                                            <h6 class="text-xs font-medium text-gray-500 mb-2">After Repair</h6>
                                                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                                                                @foreach($item->repairImages->where('image_type', 'after') as $image)
                                                                    <a href="{{ Storage::url($image->image_path) }}" target="_blank" class="block">
                                                                        <img src="{{ Storage::url($image->image_path) }}" alt="After repair" class="object-cover h-24 w-full rounded-lg">
                                                                        @if($image->caption)
                                                                            <p class="text-xs text-gray-500 mt-1 truncate">{{ $image->caption }}</p>
                                                                        @endif
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    <!-- Documentation Images -->
                                                    @if($item->repairImages->where('image_type', 'documentation')->count() > 0)
                                                        <div class="mb-4">
                                                            <h6 class="text-xs font-medium text-gray-500 mb-2">Documentation</h6>
                                                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                                                                @foreach($item->repairImages->where('image_type', 'documentation') as $image)
                                                                    <a href="{{ Storage::url($image->image_path) }}" target="_blank" class="block">
                                                                        <img src="{{ Storage::url($image->image_path) }}" alt="Documentation" class="object-cover h-24 w-full rounded-lg">
                                                                        @if($image->caption)
                                                                            <p class="text-xs text-gray-500 mt-1 truncate">{{ $image->caption }}</p>
                                                                        @endif
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif
                                            
                                            <!-- Image Upload Form -->
                                            <div class="border-t border-gray-200 pt-4">
                                                <h5 class="text-sm font-medium text-gray-700 mb-2">Upload Work Photos</h5>
                                                <form action="{{ route('vendor.inspections.upload-images', $item->id) }}#item-{{ $item->id }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                                    @csrf
                                                    
                                                    <div>
                                                        <label for="image_type_{{ $item->id }}" class="block text-sm font-medium text-gray-700">Image Type</label>
                                                        <select id="image_type_{{ $item->id }}" name="image_type" class="mt-1 block w-full py-2 px-3 border border-gray-300 bg-white rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                            <option value="before">Before Repair</option>
                                                            <option value="after">After Repair</option>
                                                            <option value="documentation">Documentation</option>
                                                        </select>
                                                    </div>
                                                    
                                                    <div>
                                                        <label for="images_{{ $item->id }}" class="block text-sm font-medium text-gray-700">Images</label>
                                                        <input type="file" name="images[]" id="images_{{ $item->id }}" multiple
                                                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                                            accept="image/*" onchange="showFileNames(this)">
                                                        <p class="mt-1 text-xs text-gray-500">Upload photos to document your work (JPG or PNG, max 5MB each)</p>
                                                    </div>
                                                    
                                                    <div id="file-list-{{ $item->id }}"></div>
                                                    
                                                    <div>
                                                        <label for="caption_{{ $item->id }}" class="block text-sm font-medium text-gray-700">Caption (Optional)</label>
                                                        <input type="text" name="caption" id="caption_{{ $item->id }}" 
                                                            class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                            placeholder="Describe what this image shows">
                                                    </div>
                                                    
                                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        Upload Images
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @else
                                        <div class="mt-4 space-y-4">
                                            @if($item->estimated_cost)
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Estimated Cost:</span>
                                                    <span class="ml-2 text-sm text-gray-900">${{ number_format($item->estimated_cost, 2) }}</span>
                                                </div>
                                            @endif

                                            @if($item->actual_cost)
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Actual Cost:</span>
                                                    <span class="ml-2 text-sm text-gray-900">${{ number_format($item->actual_cost, 2) }}</span>
                                                </div>
                                            @endif

                                            @if($item->notes)
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Notes:</span>
                                                    <p class="mt-1 text-sm text-gray-600">{{ $item->notes }}</p>
                                                </div>
                                            @endif

                                            @if($item->completion_notes)
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Completion Notes:</span>
                                                    <p class="mt-1 text-sm text-gray-600">{{ $item->completion_notes }}</p>
                                                </div>
                                            @endif

                                            <!-- Display repair images for completed/cancelled items -->
                                            @if($item->repairImages->count() > 0)
                                                <div class="mt-4">
                                                    <span class="text-sm font-medium text-gray-500">Work Photos:</span>
                                                    
                                                    <!-- Display all types of images in tabs or sections -->
                                                    @if($item->repairImages->where('image_type', 'before')->count() > 0)
                                                        <div class="mt-2 mb-3">
                                                            <h6 class="text-xs font-medium text-gray-500 mb-1">Before Repair</h6>
                                                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                                                                @foreach($item->repairImages->where('image_type', 'before') as $image)
                                                                    <a href="{{ Storage::url($image->image_path) }}" target="_blank" class="block">
                                                                        <img src="{{ Storage::url($image->image_path) }}" alt="Before repair" class="object-cover h-24 w-full rounded-lg">
                                                                        @if($image->caption)
                                                                            <p class="text-xs text-gray-500 mt-1 truncate">{{ $image->caption }}</p>
                                                                        @endif
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($item->repairImages->where('image_type', 'after')->count() > 0)
                                                        <div class="mt-2 mb-3">
                                                            <h6 class="text-xs font-medium text-gray-500 mb-1">After Repair</h6>
                                                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                                                                @foreach($item->repairImages->where('image_type', 'after') as $image)
                                                                    <a href="{{ Storage::url($image->image_path) }}" target="_blank" class="block">
                                                                        <img src="{{ Storage::url($image->image_path) }}" alt="After repair" class="object-cover h-24 w-full rounded-lg">
                                                                        @if($image->caption)
                                                                            <p class="text-xs text-gray-500 mt-1 truncate">{{ $image->caption }}</p>
                                                                        @endif
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                    
                                                    @if($item->repairImages->where('image_type', 'documentation')->count() > 0)
                                                        <div class="mt-2 mb-3">
                                                            <h6 class="text-xs font-medium text-gray-500 mb-1">Documentation</h6>
                                                            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                                                                @foreach($item->repairImages->where('image_type', 'documentation') as $image)
                                                                    <a href="{{ Storage::url($image->image_path) }}" target="_blank" class="block">
                                                                        <img src="{{ Storage::url($image->image_path) }}" alt="Documentation" class="object-cover h-24 w-full rounded-lg">
                                                                        @if($image->caption)
                                                                            <p class="text-xs text-gray-500 mt-1 truncate">{{ $image->caption }}</p>
                                                                        @endif
                                                                    </a>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endif

                                            @if($item->photos && count($item->photos) > 0)
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Photos:</span>
                                                    <div class="mt-2 grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-4">
                                                        @foreach($item->photos as $photo)
                                                            <a href="{{ Storage::url($photo) }}" target="_blank" class="block">
                                                                <img src="{{ Storage::url($photo) }}" alt="Inspection photo" class="object-cover h-24 w-full rounded-lg">
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif

                                            @if($item->completed_at)
                                                <div>
                                                    <span class="text-sm font-medium text-gray-500">Completed:</span>
                                                    <span class="ml-2 text-sm text-gray-900">{{ $item->completed_at->format('M d, Y h:ia') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
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
    // Extract the item ID from the input ID
    const itemId = input.id.split('_')[1];
    
    // Create a file list div if it doesn't exist
    let filesDiv = document.getElementById('file-list-' + itemId);
    if (!filesDiv) {
        filesDiv = document.createElement('div');
        filesDiv.id = 'file-list-' + itemId;
        filesDiv.className = 'mt-2 text-xs text-gray-600';
        input.parentNode.appendChild(filesDiv);
    } else {
        filesDiv.innerHTML = '';
    }
    
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

// Scroll to the anchor on page load if a hash is present
document.addEventListener('DOMContentLoaded', function() {
    if (window.location.hash) {
        const element = document.querySelector(window.location.hash);
        if (element) {
            // Add a slight delay to ensure the page is fully loaded
            setTimeout(() => {
                element.scrollIntoView({ behavior: 'smooth' });
            }, 300);
        }
    }
});
</script>
@endpush 