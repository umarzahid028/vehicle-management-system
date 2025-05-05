@props([
    'name',
    'label',
    'options' => [],
    'selected' => [],
    'placeholder' => 'Select options...',
    'helpText' => null
])

<div x-data="{
    open: false,
    search: '',
    selectedOptions: @js(is_array($selected) ? $selected : []),
    options: @js($options),
    toggleOption(value) {
        const index = this.selectedOptions.indexOf(value);
        if (index === -1) {
            this.selectedOptions.push(value);
        } else {
            this.selectedOptions.splice(index, 1);
        }
    },
    filteredOptions() {
        return Object.entries(this.options).filter(([key, label]) => 
            label.toLowerCase().includes(this.search.toLowerCase())
        );
    },
    getSelectedLabels() {
        return this.selectedOptions
            .map(value => this.options[value])
            .filter(label => label) // Filter out undefined values
            .join(', ');
    }
}">
    <div class="relative" @click.away="open = false">
        <x-input-label :for="$name" :value="$label" />
        
        <!-- Hidden input for form submission -->
        <template x-for="option in selectedOptions" :key="option">
            <input type="hidden" :name="`{{ $name }}[]`" :value="option">
        </template>

        <!-- Searchable input -->
        <div @click="open = !open" 
             class="mt-1 block w-full rounded-md border border-gray-300 bg-white px-3 py-2 shadow-sm focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500 cursor-pointer">
            <div class="flex items-center justify-between">
                <div class="truncate" x-text="selectedOptions.length ? getSelectedLabels() : '{{ $placeholder }}'"></div>
                <button type="button" class="ml-2">
                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="none" stroke="currentColor">
                        <path d="M7 7l3-3 3 3m0 6l-3 3-3-3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Dropdown -->
        <div x-show="open" 
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="absolute z-50 mt-1 w-full rounded-md bg-white shadow-lg">
            <div class="p-2">
                <input type="text" 
                       x-model="search" 
                       @click.stop
                       placeholder="Search..."
                       class="block w-full rounded-md border-0 py-1.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
            <ul class="max-h-60 overflow-auto py-1">
                <template x-for="[value, label] in filteredOptions()" :key="value">
                    <li @click.stop="toggleOption(value)"
                        class="relative cursor-pointer select-none py-2 pl-3 pr-9 text-gray-900 hover:bg-indigo-50">
                        <div class="flex items-center">
                            <input type="checkbox" 
                                   :checked="selectedOptions.includes(value)"
                                   @click.stop="toggleOption(value)"
                                   class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                            <span class="ml-3 block truncate" x-text="label"></span>
                        </div>
                    </li>
                </template>
            </ul>
        </div>
    </div>
    
    @if($helpText)
        <p class="mt-1 text-xs text-gray-500">{{ $helpText }}</p>
    @endif
    
    <x-input-error :messages="$errors->get($name)" class="mt-2" />
</div> 