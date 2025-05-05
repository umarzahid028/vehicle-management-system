<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Vehicle Inspection') }}: {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
            </h2>
            <a href="{{ route('vehicles.show', $vehicle) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                Back to Vehicle
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <!-- Vehicle Info Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Vehicle Information</h3>
                            <p class="text-gray-600">{{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}</p>
                            <p class="text-gray-600">VIN: {{ $vehicle->vin }}</p>
                            <p class="text-gray-600">Stock #: {{ $vehicle->stock_number }}</p>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                    $vehicle->status === 'arrived' ? 'bg-green-100 text-green-800' : 
                                    ($vehicle->status === 'delivered' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') 
                                }}">
                                    Vehicle Status: {{ ucfirst($vehicle->status) }}
                                </span>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Legend</h3>
                            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                                <div class="flex items-center bg-green-50 p-4 rounded-lg border border-green-200 shadow-sm hover:shadow transition-all duration-200">
                                    <div class="flex-shrink-0 w-3 h-3 bg-green-500 rounded-full ring-2 ring-green-500 ring-opacity-25 mr-3"></div>
                                    <div>
                                        <p class="text-sm font-semibold text-green-700">Pass</p>
                                        <p class="text-xs text-green-600">No issues found</p>
                                    </div>
                                </div>
                                <div class="flex items-center bg-yellow-50 p-4 rounded-lg border border-yellow-200 shadow-sm hover:shadow transition-all duration-200">
                                    <div class="flex-shrink-0 w-3 h-3 bg-yellow-500 rounded-full ring-2 ring-yellow-500 ring-opacity-25 mr-3"></div>
                                    <div>
                                        <p class="text-sm font-semibold text-yellow-700">Repair</p>
                                        <p class="text-xs text-yellow-600">Needs repair</p>
                                    </div>
                                </div>
                                <div class="flex items-center bg-red-50 p-4 rounded-lg border border-red-200 shadow-sm hover:shadow transition-all duration-200">
                                    <div class="flex-shrink-0 w-3 h-3 bg-red-500 rounded-full ring-2 ring-red-500 ring-opacity-25 mr-3"></div>
                                    <div>
                                        <p class="text-sm font-semibold text-red-700">Replace</p>
                                        <p class="text-xs text-red-600">Needs replacement</p>
                                    </div>
                                </div>
                                <div class="flex items-center bg-gray-50 p-4 rounded-lg border border-gray-200 shadow-sm hover:shadow transition-all duration-200">
                                    <div class="flex-shrink-0 w-3 h-3 bg-gray-400 rounded-full ring-2 ring-gray-400 ring-opacity-25 mr-3"></div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-700">Pending</p>
                                        <p class="text-xs text-gray-600">Not inspected yet</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <p class="font-bold">Please fix the following errors:</p>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Manager Inspection Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    
                    <form id="inspection-form" method="POST" 
                        action="{{ isset($existingInspection) 
                            ? route('inspection.comprehensive.update', $vehicle) 
                            : route('inspection.comprehensive.store', $vehicle) }}" 
                        class="space-y-4" enctype="multipart/form-data">
                        @csrf
                        @if(isset($existingInspection))
                            @method('PUT')
                        @endif
                        <input type="hidden" name="save_as_draft" id="save_as_draft" value="0">
                        
                        <!-- Global Vendor Selection -->
                        <div class="mb-6 bg-gray-50 p-4 rounded-lg border border-gray-200">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Manager Inspection</h3>

                            </div>
                        </div>

                        <!-- Stages Tabs -->
                        <div class="mb-6 border-b border-gray-200">
                            <div class="flex overflow-x-auto">
                                @foreach($stages as $index => $stage)
                                    <button type="button" 
                                            class="stage-tab px-4 py-2 text-sm font-medium {{ $index === 0 ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-gray-500 hover:text-gray-700' }}"
                                            data-stage-id="{{ $stage->id }}">
                                        {{ $stage->name }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    
                        <!-- Stage Content -->
                        @foreach($stages as $index => $stage)
                            <div id="stage-{{ $stage->id }}" class="stage-content {{ $index === 0 ? 'block' : 'hidden' }}">
                                <div class="space-y-6">
                                    @foreach($stage->inspectionItems as $item)
                                        <!-- Template for each inspection item -->
                                        <div class="border-b border-gray-200 mb-4 pb-4 item-container" data-stage-id="{{ $item->inspectionStage->id }}">
                                            <div class="flex flex-col lg:flex-row lg:items-start lg:space-x-4">
                                                <div class="flex-grow">
                                                    <div class="flex flex-col">
                                                        <div class="font-semibold text-gray-900">{{ $item->name }}</div>
                                                        @if($item->description)
                                                            <div class="text-sm text-gray-600 mt-1">{{ $item->description }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                                
                                                <div class="mt-3 lg:mt-0 space-y-3">
                                                    <!-- Assessment Status -->
                                                    <div class="flex flex-wrap gap-4">
                                                        <label class="relative flex items-center group">
                                                            <input type="radio" 
                                                                class="peer sr-only item-status-radio" 
                                                                name="items[{{ $item->id }}][status]" 
                                                                value="pass" 
                                                                data-item-id="{{ $item->id }}"
                                                                {{ isset($existingInspection) && $existingInspection->itemResults->where('inspection_item_id', $item->id)->first()?->status === 'pass' ? 'checked' : '' }}
                                                                {{ old("items.{$item->id}.status") == 'pass' ? 'checked' : '' }} 
                                                                form="inspection-form">
                                                            <div class="flex items-center px-6 py-2.5 rounded-lg border-2 cursor-pointer
                                                                text-sm font-medium transition-all duration-200
                                                                border-green-200 text-green-700 bg-green-50
                                                                peer-checked:bg-green-100 peer-checked:border-green-500
                                                                hover:bg-green-100 hover:border-green-300
                                                                group-hover:scale-102 transform">
                                                                <div class="w-3 h-3 bg-green-500 rounded-full ring-2 ring-green-500 ring-opacity-25 mr-3"></div>
                                                                Pass
                                                            </div>
                                                        </label>
                                                        <label class="relative flex items-center group">
                                                            <input type="radio" 
                                                                class="peer sr-only item-status-radio" 
                                                                name="items[{{ $item->id }}][status]" 
                                                                value="warning" 
                                                                data-item-id="{{ $item->id }}"
                                                                {{ isset($existingInspection) && $existingInspection->itemResults->where('inspection_item_id', $item->id)->first()?->status === 'warning' ? 'checked' : '' }}
                                                                {{ old("items.{$item->id}.status") == 'warning' ? 'checked' : '' }} 
                                                                form="inspection-form">
                                                            <div class="flex items-center px-6 py-2.5 rounded-lg border-2 cursor-pointer
                                                                text-sm font-medium transition-all duration-200
                                                                border-yellow-200 text-yellow-700 bg-yellow-50
                                                                peer-checked:bg-yellow-100 peer-checked:border-yellow-500
                                                                hover:bg-yellow-100 hover:border-yellow-300
                                                                group-hover:scale-102 transform">
                                                                <div class="w-3 h-3 bg-yellow-500 rounded-full ring-2 ring-yellow-500 ring-opacity-25 mr-3"></div>
                                                                Repair
                                                            </div>
                                                        </label>
                                                        <label class="relative flex items-center group">
                                                            <input type="radio" 
                                                                class="peer sr-only item-status-radio" 
                                                                name="items[{{ $item->id }}][status]" 
                                                                value="fail" 
                                                                data-item-id="{{ $item->id }}"
                                                                {{ isset($existingInspection) && $existingInspection->itemResults->where('inspection_item_id', $item->id)->first()?->status === 'fail' ? 'checked' : '' }}
                                                                {{ old("items.{$item->id}.status") == 'fail' ? 'checked' : '' }} 
                                                                form="inspection-form">
                                                            <div class="flex items-center px-6 py-2.5 rounded-lg border-2 cursor-pointer
                                                                text-sm font-medium transition-all duration-200
                                                                border-red-200 text-red-700 bg-red-50
                                                                peer-checked:bg-red-100 peer-checked:border-red-500
                                                                hover:bg-red-100 hover:border-red-300
                                                                group-hover:scale-102 transform">
                                                                <div class="w-3 h-3 bg-red-500 rounded-full ring-2 ring-red-500 ring-opacity-25 mr-3"></div>
                                                                Replace
                                                            </div>
                                                        </label>
                                                    </div>
                                                    
                                                    <!-- Notes field -->
                                                    <div class="w-full">
                                                        <textarea name="items[{{ $item->id }}][notes]" rows="2" form="inspection-form"
                                                            class="w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                                            placeholder="Add inspection notes...">{{ isset($existingInspection) ? ($existingInspection->itemResults->where('inspection_item_id', $item->id)->first()?->notes ?? '') : old("items.{$item->id}.notes") }}</textarea>
                                                    </div>

                                                    <!-- Image Upload -->
                                                    <div class="w-full">
                                                        <label class="block text-xs font-medium text-gray-700 mb-1">
                                                            Upload Images
                                                            <span class="text-gray-500">(Optional)</span>
                                                        </label>
                                                        <div class="mt-1 flex items-center gap-2">
                                                            <input type="file" 
                                                                name="items[{{ $item->id }}][images][]" 
                                                                accept="image/*" 
                                                                multiple
                                                                class="block w-full text-sm text-gray-500
                                                                    file:mr-4 file:py-2 file:px-4
                                                                    file:rounded-md file:border-0
                                                                    file:text-sm file:font-semibold
                                                                    file:bg-indigo-50 file:text-indigo-700
                                                                    hover:file:bg-indigo-100"
                                                            >
                                                        </div>
                                                        <p class="mt-1 text-xs text-gray-500">
                                                            You can upload multiple images. Supported formats: JPG, PNG (max 5MB each)
                                                        </p>
                                                    </div>
                                                    
                                                    <!-- Vendor field - conditionally shown -->
                                                    <div id="vendor-field-{{ $item->id }}" class="w-full hidden">
                                                        <label for="vendor_{{ $item->id }}" class="block text-xs font-medium text-gray-700 mb-1">Select Vendor:</label>
                                                        <select id="vendor_{{ $item->id }}" name="items[{{ $item->id }}][vendor_id]" form="inspection-form"
                                                            class="w-full text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm item-vendor-select select">
                                                            <option value="">Select vendor (optional)</option>
                                                            @foreach($vendors as $vendor)
                                                                <option value="{{ $vendor->id }}" 
                                                                    {{ isset($existingInspection) && $existingInspection->itemResults->where('inspection_item_id', $item->id)->first()?->vendor_id == $vendor->id ? 'selected' : '' }}
                                                                    {{ old("items.{$item->id}.vendor_id") == $vendor->id ? 'selected' : '' }}>
                                                                    {{ $vendor->name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    
                                                    <!-- Cost field - conditionally shown -->
                                                    @if($item->cost_tracking)
                                                    <div id="cost-field-{{ $item->id }}" class="w-full hidden">
                                                        <label for="cost_{{ $item->id }}" class="block text-xs font-medium text-gray-700 mb-1">Estimated Cost:</label>
                                                        <div class="relative rounded-md shadow-sm">
                                                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                                                <span class="text-gray-500 sm:text-sm">$</span>
                                                            </div>
                                                            <input type="number" step="0.01" min="0" id="cost_{{ $item->id }}" name="items[{{ $item->id }}][cost]" form="inspection-form"
                                                                value="{{ isset($existingInspection) ? ($existingInspection->itemResults->where('inspection_item_id', $item->id)->first()?->cost ?? '') : old("items.{$item->id}.cost") }}" 
                                                                class="w-full pl-7 text-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                                                placeholder="0.00">
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </form>
                </div>
            </div>

            <!-- Vendor Assignment Section -->
            <div class="mt-8 p-4 bg-white border border-gray-200 rounded-lg shadow">
               

                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">
                    <span class="text-blue-600"><i class="fas fa-tools mr-2"></i>Vendor Assignment</span>
                </h3>
                
                <div class="text-sm bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-yellow-700">
                                <strong>Note:</strong> For items that require repairs, you can assign a specific vendor to each item by using the vendor dropdown next to each failing item. If you don't assign a specific vendor, the global vendor above will be used (if selected).
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation/Action Buttons -->
            <div class="flex justify-between mt-6">
                <div class="flex space-x-2">
                    <button type="button" id="prev-stage" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50 disabled:cursor-not-allowed">
                        <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                        Previous Stage
                    </button>
                    <button type="button" id="next-stage" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Next Stage
                        <x-heroicon-o-arrow-right class="h-4 w-4 ml-1" />
                    </button>
                </div>
                
                <div class="flex space-x-2">
                    <button type="button" id="save-draft" class="inline-flex items-center px-6 py-3 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <x-heroicon-o-document-text class="h-4 w-4 mr-1" />
                        Save Draft
                    </button>
                    <button type="submit" form="inspection-form" class="inline-flex items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <x-heroicon-o-check-circle class="h-4 w-4 mr-1" />
                       Save & Assign Vendors
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Stage navigation
            const stages = document.querySelectorAll('.stage-content');
            const stageTabs = document.querySelectorAll('.stage-tab');
            const prevStageBtn = document.getElementById('prev-stage');
            const nextStageBtn = document.getElementById('next-stage');
            let currentStageIndex = 0;

            function showStage(index) {
                stages.forEach(stage => stage.classList.add('hidden'));
                stageTabs.forEach(tab => {
                    tab.classList.remove('text-indigo-600', 'border-b-2', 'border-indigo-600');
                    tab.classList.add('text-gray-500');
                });

                stages[index].classList.remove('hidden');
                stageTabs[index].classList.add('text-indigo-600', 'border-b-2', 'border-indigo-600');
                stageTabs[index].classList.remove('text-gray-500');

                // Update navigation buttons
                prevStageBtn.disabled = index === 0;
                nextStageBtn.disabled = index === stages.length - 1;

                currentStageIndex = index;
            }

            // Stage tab click handlers
            stageTabs.forEach((tab, index) => {
                tab.addEventListener('click', () => showStage(index));
            });

            // Previous/Next stage button handlers
            prevStageBtn.addEventListener('click', () => {
                if (currentStageIndex > 0) {
                    showStage(currentStageIndex - 1);
                }
            });

            nextStageBtn.addEventListener('click', () => {
                if (currentStageIndex < stages.length - 1) {
                    showStage(currentStageIndex + 1);
                }
            });

            // Initialize first stage
            showStage(0);

            // Handle status radio changes
            const statusRadios = document.querySelectorAll('.item-status-radio');
            statusRadios.forEach(radio => {
                radio.addEventListener('change', function() {
                    const itemId = this.dataset.itemId;
                    const vendorField = document.getElementById(`vendor-field-${itemId}`);
                    const costField = document.getElementById(`cost-field-${itemId}`);
                    const vendorSelect = vendorField ? vendorField.querySelector('select') : null;
                    
                    if (vendorField) {
                        if (this.value === 'warning' || this.value === 'fail') {
                            vendorField.classList.remove('hidden');
                            // If there's a global vendor selected and no vendor is currently selected for this item
                            const globalVendorId = document.getElementById('vendor_id').value;
                            if (globalVendorId && vendorSelect && !vendorSelect.value) {
                                vendorSelect.value = globalVendorId;
                            }
                        } else {
                            vendorField.classList.add('hidden');
                            if (vendorSelect) vendorSelect.value = '';
                        }
                    }
                    
                    if (costField) {
                        if (this.value === 'warning' || this.value === 'fail') {
                            costField.classList.remove('hidden');
                        } else {
                            costField.classList.add('hidden');
                            const costInput = costField.querySelector('input[type="number"]');
                            if (costInput) costInput.value = '';
                        }
                    }
                    
                    updateTotalCost();
                });

                // Trigger change event on page load for existing selections
                if (radio.checked) {
                    radio.dispatchEvent(new Event('change'));
                }
            });

            // Handle batch vendor assignment
            const batchAssignVendorBtn = document.getElementById('batch-assign-vendor');
            const globalVendorSelect = document.getElementById('vendor_id');

            batchAssignVendorBtn.addEventListener('click', function() {
                const selectedVendorId = globalVendorSelect.value;
                if (!selectedVendorId) {
                    alert('Please select a vendor first.');
                    return;
                }

                console.log('Starting vendor assignment process...');
                console.log('Selected vendor ID:', selectedVendorId);
                
                let assignedCount = 0;
                
                // Find all items that need repair (marked as warning or fail)
                document.querySelectorAll('.item-container').forEach(container => {
                    // Find the item ID from the container
                    const statusRadios = container.querySelectorAll('.item-status-radio');
                    if (statusRadios.length === 0) {
                        console.log('No status radios found in container');
                        return;
                    }
                    
                    // Get the item ID from the first radio
                    const itemId = statusRadios[0].dataset.itemId;
                    console.log('Processing item:', itemId);
                    
                    // Check if this item needs repair (warning or fail)
                    const needsRepair = Array.from(statusRadios).some(radio => 
                        radio.checked && (radio.value === 'warning' || radio.value === 'fail')
                    );
                    
                    if (needsRepair) {
                        console.log('Item needs repair:', itemId);
                        
                        // Find the vendor field and select element
                        const vendorField = document.getElementById(`vendor-field-${itemId}`);
                        console.log('Vendor field:', vendorField ? 'found' : 'not found');
                        
                        if (vendorField) {
                            const vendorSelect = vendorField.querySelector('select');
                            
                            if (vendorSelect) {
                                // Show the vendor field
                                vendorField.classList.remove('hidden');
                                
                                // Set the vendor
                                vendorSelect.value = selectedVendorId;
                                assignedCount++;
                                console.log('Vendor assigned to item:', itemId);
                            } else {
                                console.log('Vendor select not found for item:', itemId);
                            }
                        } else {
                            console.log('Vendor field not found for item:', itemId);
                        }
                    } else {
                        console.log('Item does not need repair:', itemId);
                    }
                });

                // Show success message
                const message = document.createElement('div');
                message.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50';
                message.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <span>Vendor successfully assigned to ${assignedCount} item${assignedCount !== 1 ? 's' : ''}</span>
                    </div>
                `;
                document.body.appendChild(message);
                setTimeout(() => message.remove(), 3000);
            });

            // Handle save draft button
            const saveDraftBtn = document.getElementById('save-draft');
            const saveAsDraftInput = document.getElementById('save_as_draft');

            saveDraftBtn.addEventListener('click', function() {
                saveAsDraftInput.value = '1';
                document.getElementById('inspection-form').submit();
            });

            // Calculate and update total cost
            function updateTotalCost() {
                const costInputs = document.querySelectorAll('input[name$="[cost]"]');
                let total = 0;

                costInputs.forEach(input => {
                    const cost = parseFloat(input.value) || 0;
                    if (!input.closest('div').classList.contains('hidden')) {
                        total += cost;
                    }
                });

                document.getElementById('estimated-total-cost').textContent = `$${total.toFixed(2)}`;
            }

            // Add event listeners for cost changes
            document.querySelectorAll('input[name$="[cost]"]').forEach(input => {
                input.addEventListener('input', updateTotalCost);
            });

            // Initial total cost calculation
            updateTotalCost();
        });
    </script>
    @endpush

    @push('styles')
    <style></style>
        .error-highlight {
            border-color: #ef4444;
            background-color: #fee2e2;
            padding: 1rem;
            border-radius: 0.375rem;
        }
    </style>
    @endpush
</x-app-layout> 