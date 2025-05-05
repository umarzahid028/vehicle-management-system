@props(['result'])

<div class="space-y-4">
    {{-- Current Images Display --}}
    @if($result->repairImages->count() > 0)
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($result->repairImages as $image)
                <div class="relative group">
                    <img src="{{ $image->image_url }}" alt="Repair Image" class="w-full h-32 object-cover rounded-lg">
                    <div class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center rounded-lg">
                        <form action="{{ route('repair-images.destroy', $image) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-white hover:text-red-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Upload Form --}}
    <form action="{{ route('inspection-item-results.upload-photo', $result) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
        @csrf
        <div class="flex items-center justify-center w-full">
            <label for="images" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                    <svg class="w-8 h-8 mb-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p class="mb-2 text-sm text-gray-500">
                        <span class="font-semibold">Click to upload</span> or drag and drop
                    </p>
                    <p class="text-xs text-gray-500">PNG, JPG or JPEG (MAX. 5MB)</p>
                </div>
                <input id="images" name="images[]" type="file" class="hidden" multiple accept="image/png,image/jpeg,image/jpg" />
            </label>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Upload Images
            </button>
        </div>
    </form>
</div> 