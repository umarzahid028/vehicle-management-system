<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Report Sales Issue') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                
                    <form action="{{ route('sales.issues.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Vehicle Selection -->
                        <div>
                            <x-input-label for="vehicle_id" :value="__('Vehicle')" />
                            <select id="vehicle_id" name="vehicle_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Select Vehicle</option>
                                @foreach($vehicles as $vehicle)
                                    <option value="{{ $vehicle->id }}" {{ old('vehicle_id', $vehicle?->id) == $vehicle->id ? 'selected' : '' }}>
                                        {{ $vehicle->stock_number }} - {{ $vehicle->year }} {{ $vehicle->make }} {{ $vehicle->model }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('vehicle_id')" class="mt-2" />
                        </div>

                        <!-- Issue Type -->
                        <div>
                            <x-input-label for="issue_type" :value="__('Issue Type')" />
                            <select id="issue_type" name="issue_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="">Select Issue Type</option>
                                @foreach($issueTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('issue_type') === $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('issue_type')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea id="description" name="description" rows="4" 
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <!-- Priority -->
                        <div>
                            <x-input-label for="priority" :value="__('Priority')" />
                            <select id="priority" name="priority" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>High</option>
                                <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            <x-input-error :messages="$errors->get('priority')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end gap-4">
                            <a href="{{ route('sales.issues.index') }}" class="text-gray-600 hover:text-gray-900">
                                {{ __('Cancel') }}
                            </a>
                            <x-primary-button>
                                {{ __('Report Issue') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 