<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Inspection Stage') }}: {{ $stage->name }}
            </h2>
            <a href="{{ route('inspection.stages.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                Back to Stages
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6 py-6">
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

                    <form method="POST" action="{{ route('inspection.stages.update', $stage) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <x-input-label for="name" :value="__('Stage Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $stage->name)" 
                                    required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <!-- Order -->
                            <div>
                                <x-input-label for="order" :value="__('Display Order')" />
                                <x-text-input id="order" class="block mt-1 w-full" type="number" name="order" 
                                    :value="old('order', $stage->order)" min="1" />
                                <x-input-error :messages="$errors->get('order')" class="mt-2" />
                                <p class="mt-1 text-xs text-gray-500">
                                    Determines the order of this stage in the inspection process. Lower numbers come first.
                                </p>
                            </div>

                            <!-- Description -->
                            <div class="md:col-span-2">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" rows="3" 
                                    class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $stage->description) }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div class="md:col-span-2">
                                <div class="flex items-center">
                                    <input id="is_active" name="is_active" type="checkbox" value="1" 
                                        {{ old('is_active', $stage->is_active) ? 'checked' : '' }}
                                        class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                        Stage is active and available for inspections
                                    </label>
                                </div>
                                <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-end">
                            <x-primary-button class="ml-4">
                                {{ __('Update Stage') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 