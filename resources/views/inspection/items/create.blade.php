<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add New Inspection Item') }}
            </h2>
            <a href="{{ route('inspection.items.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                Back to Items
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
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

                    <form method="POST" action="{{ route('inspection.items.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <x-input-label for="name" :value="__('Item Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" 
                                    required autofocus placeholder="Enter item name" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Inspection Stage -->
                            <div>
                                <x-input-label for="inspection_stage_id" :value="__('Inspection Stage')" />
                                <select id="inspection_stage_id" name="inspection_stage_id" 
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    required>
                                    <option value="">Select a stage</option>
                                    @foreach($stages as $stage)
                                        @if(is_object($stage))
                                            <option value="{{ $stage->id }}" {{ old('inspection_stage_id', $stageId ?? null) == $stage->id ? 'selected' : '' }}>
                                                {{ $stage->name }}
                                            </option>
                                        @elseif(is_array($stage) && isset($stage['id']))
                                            <option value="{{ $stage['id'] }}" {{ old('inspection_stage_id', $stageId ?? null) == $stage['id'] ? 'selected' : '' }}>
                                                {{ $stage['name'] ?? 'Unknown Stage' }}
                                            </option>
                                        @else
                                            <option value="{{ $stage }}" {{ old('inspection_stage_id', $stageId ?? null) == $stage ? 'selected' : '' }}>
                                                {{ $stage }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('inspection_stage_id')" class="mt-2" />
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" rows="3" 
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                    placeholder="Describe what this item inspects and criteria">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <!-- Vendor Required -->
                            <div>
                                <div class="flex items-center">
                                    <input id="vendor_required" name="vendor_required" type="checkbox" value="1" 
                                        {{ old('vendor_required') ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <label for="vendor_required" class="ml-2 block text-sm text-gray-900">
                                        Vendor required for repairs
                                    </label>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Check if this item typically requires a vendor for repairs
                                </p>
                                <x-input-error :messages="$errors->get('vendor_required')" class="mt-2" />
                            </div>

                            <!-- Cost Tracking -->
                            <div>
                                <div class="flex items-center">
                                    <input id="cost_tracking" name="cost_tracking" type="checkbox" value="1" 
                                        {{ old('cost_tracking') ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <label for="cost_tracking" class="ml-2 block text-sm text-gray-900">
                                        Track cost for this item
                                    </label>
                                </div>
                                <p class="mt-1 text-xs text-gray-500">
                                    Check if you want to track repair costs for this item
                                </p>
                                <x-input-error :messages="$errors->get('cost_tracking')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div class="md:col-span-2">
                                <div class="flex items-center">
                                    <input id="is_active" name="is_active" type="checkbox" value="1" 
                                        {{ old('is_active', true) ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                        Item is active and available for inspections
                                    </label>
                                </div>
                                <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button class="ml-4">
                                {{ __('Create Item') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 