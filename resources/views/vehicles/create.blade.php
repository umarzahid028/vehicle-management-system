<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div class="flex items-center space-x-4">
                <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
                    {{ __('Add New Vehicle') }}
                </h2>
                <span class="text-sm text-gray-500">Fill in the details below to add a new vehicle to inventory</span>
            </div>
            <div>
                <a href="{{ route('vehicles.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    <x-heroicon-o-arrow-left class="h-4 w-4 mr-2" />
                    {{ __('Back to List') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <form action="{{ route('vehicles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                @if ($errors->any())
                    <div class="rounded-lg bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <x-heroicon-m-x-circle class="h-5 w-5 text-red-400" />
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    There {{ $errors->count() === 1 ? 'is' : 'are' }} {{ $errors->count() }} {{ Str::plural('error', $errors->count()) }} with your submission
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc space-y-1 pl-5">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Basic Information Card -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="md:grid md:grid-cols-3 md:gap-6">
                            <div class="md:col-span-1">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Basic Information</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Enter the core details of the vehicle. Fields marked with * are required.
                                </p>
                            </div>
                            <div class="mt-5 md:col-span-2 md:mt-0">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="stock_number" class="block text-sm font-medium text-gray-700">
                                            Stock Number <span class="text-red-500">*</span>
                                        </label>
                                        <x-shadcn.input 
                                            type="text" 
                                            name="stock_number" 
                                            id="stock_number" 
                                            :value="old('stock_number')" 
                                            class="mt-1"
                                            required 
                                            placeholder="Enter stock number"
                                        />
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="vin" class="block text-sm font-medium text-gray-700">
                                            VIN <span class="text-red-500">*</span>
                                        </label>
                                        <x-shadcn.input 
                                            type="text" 
                                            name="vin" 
                                            id="vin" 
                                            :value="old('vin')" 
                                            class="mt-1"
                                            required 
                                            placeholder="Enter VIN number"
                                        />
                                    </div>

                                    <div class="col-span-6 sm:col-span-2">
                                        <label for="year" class="block text-sm font-medium text-gray-700">
                                            Year <span class="text-red-500">*</span>
                                        </label>
                                        <x-shadcn.select 
                                            name="year" 
                                            id="year" 
                                            class="mt-1"
                                            required
                                        >
                                            <option value="">Select Year</option>
                                            @for ($i = date('Y') + 1; $i >= 1900; $i--)
                                                <option value="{{ $i }}" {{ old('year') == $i ? 'selected' : '' }}>
                                                    {{ $i }}
                                                </option>
                                            @endfor
                                        </x-shadcn.select>
                                    </div>

                                    <div class="col-span-6 sm:col-span-2">
                                        <label for="make" class="block text-sm font-medium text-gray-700">
                                            Make <span class="text-red-500">*</span>
                                        </label>
                                        <x-shadcn.input 
                                            type="text" 
                                            name="make" 
                                            id="make" 
                                            :value="old('make')" 
                                            class="mt-1"
                                            required 
                                            placeholder="Enter make"
                                        />
                                    </div>

                                    <div class="col-span-6 sm:col-span-2">
                                        <label for="model" class="block text-sm font-medium text-gray-700">
                                            Model <span class="text-red-500">*</span>
                                        </label>
                                        <x-shadcn.input 
                                            type="text" 
                                            name="model" 
                                            id="model" 
                                            :value="old('model')" 
                                            class="mt-1"
                                            required 
                                            placeholder="Enter model"
                                        />
                                    </div>

                                    <div class="col-span-6">
                                        <label for="trim" class="block text-sm font-medium text-gray-700">
                                            Trim
                                        </label>
                                        <x-shadcn.input 
                                            type="text" 
                                            name="trim" 
                                            id="trim" 
                                            :value="old('trim')" 
                                            class="mt-1"
                                            placeholder="Enter trim level (optional)"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Vehicle Details Card -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="md:grid md:grid-cols-3 md:gap-6">
                            <div class="md:col-span-1">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Vehicle Details</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Provide detailed specifications and features of the vehicle.
                                </p>
                            </div>
                            <div class="mt-5 md:col-span-2 md:mt-0">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="odometer" class="block text-sm font-medium text-gray-700">
                                            Odometer
                                        </label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <x-shadcn.input 
                                                type="number" 
                                                name="odometer" 
                                                id="odometer" 
                                                :value="old('odometer')" 
                                                class="pr-12"
                                                placeholder="Enter mileage"
                                            />
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                                <span class="text-gray-500 sm:text-sm">miles</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="body_type" class="block text-sm font-medium text-gray-700">Body Type</label>
                                        <x-shadcn.select name="body_type" id="body_type" class="mt-1">
                                            <option value="">Select Body Type</option>
                                            @foreach(['Sedan', 'SUV', 'Truck', 'Van', 'Coupe', 'Wagon', 'Convertible', 'Hatchback'] as $type)
                                                <option value="{{ $type }}" {{ old('body_type') == $type ? 'selected' : '' }}>
                                                    {{ $type }}
                                                </option>
                                            @endforeach
                                        </x-shadcn.select>
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="exterior_color" class="block text-sm font-medium text-gray-700">Exterior Color</label>
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            <x-shadcn.input 
                                                type="text" 
                                                name="exterior_color" 
                                                id="exterior_color" 
                                                :value="old('exterior_color')" 
                                                placeholder="e.g., Midnight Black"
                                            />
                                        </div>
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="interior_color" class="block text-sm font-medium text-gray-700">Interior Color</label>
                                        <div class="mt-1 flex rounded-md shadow-sm">
                                            <x-shadcn.input 
                                                type="text" 
                                                name="interior_color" 
                                                id="interior_color" 
                                                :value="old('interior_color')" 
                                                placeholder="e.g., Beige Leather"
                                            />
                                        </div>
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="drive_train" class="block text-sm font-medium text-gray-700">Drive Train</label>
                                        <x-shadcn.select name="drive_train" id="drive_train" class="mt-1">
                                            <option value="">Select Drive Train</option>
                                            @foreach(['FWD', 'RWD', 'AWD', '4WD'] as $driveTrain)
                                                <option value="{{ $driveTrain }}" {{ old('drive_train') == $driveTrain ? 'selected' : '' }}>
                                                    {{ $driveTrain }}
                                                </option>
                                            @endforeach
                                        </x-shadcn.select>
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="transmission" class="block text-sm font-medium text-gray-700">Transmission</label>
                                        <x-shadcn.select name="transmission" id="transmission" class="mt-1">
                                            <option value="">Select Transmission</option>
                                            @foreach(['Automatic', 'Manual', 'CVT', 'DCT', 'Semi-Automatic'] as $transmission)
                                                <option value="{{ $transmission }}" {{ old('transmission') == $transmission ? 'selected' : '' }}>
                                                    {{ $transmission }}
                                                </option>
                                            @endforeach
                                        </x-shadcn.select>
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="engine" class="block text-sm font-medium text-gray-700">Engine</label>
                                        <x-shadcn.input 
                                            type="text" 
                                            name="engine" 
                                            id="engine" 
                                            :value="old('engine')" 
                                            class="mt-1"
                                            placeholder="e.g., 2.0L 4-Cylinder"
                                        />
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="fuel_type" class="block text-sm font-medium text-gray-700">Fuel Type</label>
                                        <x-shadcn.select name="fuel_type" id="fuel_type" class="mt-1">
                                            <option value="">Select Fuel Type</option>
                                            @foreach(['Gasoline', 'Diesel', 'Electric', 'Hybrid', 'Plug-in Hybrid'] as $fuelType)
                                                <option value="{{ $fuelType }}" {{ old('fuel_type') == $fuelType ? 'selected' : '' }}>
                                                    {{ $fuelType }}
                                                </option>
                                            @endforeach
                                        </x-shadcn.select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Pricing & Status Card -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="md:grid md:grid-cols-3 md:gap-6">
                            <div class="md:col-span-1">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Pricing & Status</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Set the vehicle's price and current status in inventory.
                                </p>
                            </div>
                            <div class="mt-5 md:col-span-2 md:mt-0">
                                <div class="grid grid-cols-6 gap-6">
                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="advertising_price" class="block text-sm font-medium text-gray-700">
                                            Advertising Price <span class="text-red-500">*</span>
                                        </label>
                                        <div class="mt-1 relative rounded-md shadow-sm">
                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                <span class="text-gray-500 sm:text-sm">$</span>
                                            </div>
                                            <x-shadcn.input 
                                                type="number" 
                                                name="advertising_price" 
                                                id="advertising_price" 
                                                :value="old('advertising_price')" 
                                                class="pl-7"
                                                step="0.01"
                                                required
                                                placeholder="0.00"
                                            />
                                        </div>
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="status" class="block text-sm font-medium text-gray-700">
                                            Status <span class="text-red-500">*</span>
                                        </label>
                                        <x-shadcn.select 
                                            name="status" 
                                            id="status" 
                                            class="mt-1"
                                            required
                                        >
                                            <option value="">Select Status</option>
                                            <option value="{{ \App\Models\Vehicle::STATUS_AVAILABLE }}" {{ old('status', \App\Models\Vehicle::STATUS_AVAILABLE) == \App\Models\Vehicle::STATUS_AVAILABLE ? 'selected' : '' }}>
                                                {{ \App\Models\Vehicle::STATUS_AVAILABLE }}
                                            </option>
                                            @foreach([
                                                \App\Models\Vehicle::STATUS_TRANSPORT_PENDING,
                                                \App\Models\Vehicle::STATUS_TRANSPORT_IN_TRANSIT,
                                                \App\Models\Vehicle::STATUS_READY_FOR_SALE,
                                                \App\Models\Vehicle::STATUS_SOLD
                                            ] as $status)
                                                <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                        </x-shadcn.select>
                                    </div>

                                    <div class="col-span-6 sm:col-span-3">
                                        <label for="date_in_stock" class="block text-sm font-medium text-gray-700">Date in Stock</label>
                                        <x-shadcn.input 
                                            type="date" 
                                            name="date_in_stock" 
                                            id="date_in_stock" 
                                            :value="old('date_in_stock')" 
                                            class="mt-1"
                                        />
                                    </div>

                                    <div class="col-span-6">
                                        <div class="relative flex items-start">
                                            <div class="flex h-6 items-center">
                                                <input 
                                                    type="checkbox" 
                                                    name="is_featured" 
                                                    id="is_featured" 
                                                    value="1" 
                                                    {{ old('is_featured') ? 'checked' : '' }}
                                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600"
                                                >
                                            </div>
                                            <div class="ml-3 text-sm leading-6">
                                                <label for="is_featured" class="font-medium text-gray-900">Featured Vehicle</label>
                                                <p class="text-gray-500">Show this vehicle prominently on the website and in featured listings.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Vehicle Images Card -->
                <div class="bg-white shadow sm:rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <div class="md:grid md:grid-cols-3 md:gap-6">
                            <div class="md:col-span-1">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Vehicle Images</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    Upload high-quality images of the vehicle. The main image will be displayed prominently.
                                </p>
                            </div>
                            <div class="mt-5 md:col-span-2 md:mt-0 space-y-6">
                                <!-- Main Image Upload -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Main Vehicle Image</label>
                                    <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10" id="main_image_drop_zone">
                                        <div class="text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                                            </svg>
                                            <div class="mt-4 flex text-sm leading-6 text-gray-600">
                                                <label for="vehicle_image" class="relative cursor-pointer rounded-md bg-white font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                                    <span>Upload main image</span>
                                                    <input id="vehicle_image" name="vehicle_image" type="file" class="sr-only" accept="image/*">
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs leading-5 text-gray-600">PNG, JPG, GIF up to 5MB</p>
                                        </div>
                                    </div>
                                    <div id="main_image_preview" class="mt-2 hidden">
                                        <div class="relative">
                                            <img src="" alt="Main image preview" class="h-48 w-full object-cover rounded-lg">
                                            <button type="button" onclick="removeMainImage()" class="absolute top-2 right-2 inline-flex items-center rounded-full border border-gray-200 bg-white p-1 text-gray-400 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Gallery Images Upload -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Additional Gallery Images</label>
                                    <div class="mt-2 flex justify-center rounded-lg border border-dashed border-gray-900/25 px-6 py-10" id="gallery_images_drop_zone">
                                        <div class="text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                                            </svg>
                                            <div class="mt-4 flex text-sm leading-6 text-gray-600">
                                                <label for="gallery_images" class="relative cursor-pointer rounded-md bg-white font-semibold text-indigo-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-indigo-600 focus-within:ring-offset-2 hover:text-indigo-500">
                                                    <span>Upload gallery images</span>
                                                    <input id="gallery_images" name="gallery_images[]" type="file" class="sr-only" multiple accept="image/*">
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs leading-5 text-gray-600">PNG, JPG, GIF up to 5MB each</p>
                                        </div>
                                    </div>
                                    <div id="gallery_images_preview" class="mt-2 hidden">
                                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                                            <!-- Gallery previews will be inserted here -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Form Actions -->
                <div class="flex items-center justify-end space-x-4">
                    <a href="{{ route('vehicles.index') }}" class="inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        Cancel
                    </a>
                    <button type="submit" class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Create Vehicle
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Main Image Preview
        const mainImageInput = document.getElementById('vehicle_image');
        const mainImagePreview = document.getElementById('main_image_preview');
        const mainImageDropZone = document.getElementById('main_image_drop_zone');

        function handleMainImage(file) {
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    mainImagePreview.querySelector('img').src = e.target.result;
                    mainImagePreview.classList.remove('hidden');
                    mainImageDropZone.classList.add('hidden');
                }
                reader.readAsDataURL(file);
            }
        }

        function removeMainImage() {
            mainImageInput.value = '';
            mainImagePreview.classList.add('hidden');
            mainImageDropZone.classList.remove('hidden');
        }

        mainImageInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                handleMainImage(this.files[0]);
            }
        });

        // Gallery Images Preview
        const galleryInput = document.getElementById('gallery_images');
        const galleryPreview = document.getElementById('gallery_images_preview');
        const galleryDropZone = document.getElementById('gallery_images_drop_zone');
        const galleryGrid = galleryPreview.querySelector('.grid');

        function handleGalleryImages(files) {
            if (files.length > 0) {
                galleryGrid.innerHTML = '';
                Array.from(files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const preview = document.createElement('div');
                        preview.className = 'relative group';
                        preview.innerHTML = `
                            <img src="${e.target.result}" alt="Gallery preview ${index + 1}" class="h-40 w-full object-cover rounded-lg">
                            <button type="button" onclick="removeGalleryImage(${index})" class="absolute top-2 right-2 inline-flex items-center rounded-full border border-gray-200 bg-white p-1 text-gray-400 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        `;
                        galleryGrid.appendChild(preview);
                    }
                    reader.readAsDataURL(file);
                });
                galleryPreview.classList.remove('hidden');
            }
        }

        function removeGalleryImage(index) {
            const dt = new DataTransfer();
            const { files } = galleryInput;
            
            for (let i = 0; i < files.length; i++) {
                if (i !== index) {
                    dt.items.add(files[i]);
                }
            }
            
            galleryInput.files = dt.files;
            if (galleryInput.files.length === 0) {
                galleryPreview.classList.add('hidden');
                galleryDropZone.classList.remove('hidden');
            } else {
                handleGalleryImages(galleryInput.files);
            }
        }

        galleryInput.addEventListener('change', function(e) {
            if (this.files && this.files.length > 0) {
                handleGalleryImages(this.files);
                galleryDropZone.classList.add('hidden');
            }
        });

        // Drag and Drop Functionality
        [mainImageDropZone, galleryDropZone].forEach(dropZone => {
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults (e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });

            function highlight(e) {
                dropZone.classList.add('border-indigo-600', 'border-2');
            }

            function unhighlight(e) {
                dropZone.classList.remove('border-indigo-600', 'border-2');
            }
        });

        mainImageDropZone.addEventListener('drop', handleMainImageDrop, false);
        galleryDropZone.addEventListener('drop', handleGalleryDrop, false);

        function handleMainImageDrop(e) {
            const dt = e.dataTransfer;
            const file = dt.files[0];
            
            if (file && file.type.startsWith('image/')) {
                mainImageInput.files = dt.files;
                handleMainImage(file);
            }
        }

        function handleGalleryDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            
            if (files.length > 0) {
                galleryInput.files = files;
                handleGalleryImages(files);
                galleryDropZone.classList.add('hidden');
            }
        }

        // Show sold date and buyer name fields when status is 'sold'
        const statusSelect = document.getElementById('status');
        const soldDateContainer = document.getElementById('sold_date_container');
        const buyerNameContainer = document.getElementById('buyer_name_container');

        statusSelect.addEventListener('change', function() {
            if (this.value === 'sold') {
                soldDateContainer.style.display = 'block';
                buyerNameContainer.style.display = 'block';
            } else {
                soldDateContainer.style.display = 'none';
                buyerNameContainer.style.display = 'none';
            }
        });
    </script>
    @endpush
</x-app-layout> 