<div class="mt-4">
    <h4 class="text-md font-medium text-gray-900 mb-2">Item Photo</h4>
    
    @if($result->photo_path)
        <div class="mb-2">
            <img src="{{ Storage::url($result->photo_path) }}" alt="Inspection Photo" class="rounded-md max-w-full h-auto max-h-48">
        </div>
        
        <form action="{{ route('inspection.results.upload-photo', $result) }}" method="POST" enctype="multipart/form-data" class="mt-2">
            @csrf
            <div class="flex items-center space-x-2">
                <input type="file" name="photo" id="photo_{{ $result->id }}" class="sr-only" accept="image/*" onchange="this.form.submit()">
                <label for="photo_{{ $result->id }}" class="inline-flex items-center px-3 py-1 bg-gray-200 text-gray-700 rounded-md text-sm font-medium hover:bg-gray-300 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM6.293 6.707a1 1 0 010-1.414l3-3a1 1 0 011.414 0l3 3a1 1 0 01-1.414 1.414L11 5.414V13a1 1 0 11-2 0V5.414L7.707 6.707a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    Replace Photo
                </label>
            </div>
        </form>
    @else
        <form action="{{ route('inspection.results.upload-photo', $result) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="flex items-center space-x-2">
                <input type="file" name="photo" id="photo_{{ $result->id }}" class="sr-only" accept="image/*" onchange="this.form.submit()">
                <label for="photo_{{ $result->id }}" class="inline-flex items-center px-3 py-1 bg-indigo-600 text-white rounded-md text-sm font-medium hover:bg-indigo-700 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 5a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V7a2 2 0 00-2-2h-1.586a1 1 0 01-.707-.293l-1.121-1.121A2 2 0 0011.172 3H8.828a2 2 0 00-1.414.586L6.293 4.707A1 1 0 015.586 5H4zm6 9a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                    </svg>
                    Upload Photo
                </label>
                <span class="text-xs text-gray-500">Click to select an image</span>
            </div>
        </form>
    @endif
</div> 