@props([
    'id',
    'name',
    'label',
    'required' => false,
    'placeholder' => '',
    'helpText' => '',
])

<div x-data="{ show: false, strength: 0 }" class="space-y-2">
    <label for="{{ $id }}" class="block text-sm font-medium text-gray-900">
        {{ $label }}
    </label>
    
    <div class="relative">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-500">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect width="18" height="11" x="3" y="11" rx="2" ry="2"/>
                <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
            </svg>
        </div>
        
        <input 
            :type="show ? 'text' : 'password'"
            id="{{ $id }}"
            name="{{ $name }}"
            {{ $required ? 'required' : '' }}
            placeholder="{{ $placeholder }}"
            x-on:input="checkStrength($event.target.value)"
            {{ $attributes->merge(['class' => 'pl-10 pr-10 py-2 w-full rounded-md border border-input bg-background text-sm shadow-sm transition-colors placeholder:text-muted-foreground focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500']) }}
        >
        
        <button 
            type="button"
            @click="show = !show"
            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none"
        >
            <svg 
                x-show="!show"
                xmlns="http://www.w3.org/2000/svg" 
                width="16" height="16" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2" 
                stroke-linecap="round" 
                stroke-linejoin="round"
            >
                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/>
                <circle cx="12" cy="12" r="3"/>
            </svg>
            <svg 
                x-show="show"
                xmlns="http://www.w3.org/2000/svg" 
                width="16" height="16" 
                viewBox="0 0 24 24" 
                fill="none" 
                stroke="currentColor" 
                stroke-width="2" 
                stroke-linecap="round" 
                stroke-linejoin="round"
                style="display: none;"
            >
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
            </svg>
        </button>
    </div>

    <!-- Password Strength Indicator (only show for password field, not confirmation) -->
    @if($name === 'password')
        <div x-show="$el.previousElementSibling.querySelector('input').value.length > 0" class="space-y-2" style="display: none;">
            <div class="h-1 w-full bg-gray-200 rounded-full overflow-hidden">
                <div 
                    class="h-full transition-all duration-300"
                    :class="{
                        'w-1/4 bg-red-500': strength === 1,
                        'w-2/4 bg-yellow-500': strength === 2,
                        'w-3/4 bg-blue-500': strength === 3,
                        'w-full bg-green-500': strength === 4
                    }"
                ></div>
            </div>
            <p class="text-xs text-gray-500" x-text="getStrengthText()"></p>
        </div>
    @endif
    
    @if($helpText)
        <p class="text-sm text-gray-500 flex items-center space-x-1">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>
            </svg>
            <span>{{ $helpText }}</span>
        </p>
    @endif
    
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

@once
@push('scripts')
<script>
    function checkStrength(password) {
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/) && password.match(/[A-Z]/)) strength++;
        if (password.match(/\d/)) strength++;
        if (password.match(/[^a-zA-Z\d]/)) strength++;
        
        this.strength = strength;
    }
    
    function getStrengthText() {
        switch (this.strength) {
            case 0:
            case 1:
                return 'Weak password';
            case 2:
                return 'Fair password';
            case 3:
                return 'Good password';
            case 4:
                return 'Strong password';
            default:
                return '';
        }
    }
</script>
@endpush
@endonce 