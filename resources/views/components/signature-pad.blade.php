@props([
    'name' => 'signature',
    'required' => false,
    'value' => '',
    'label' => 'Signature',
    'height' => 200,
    'width' => null,
    'showControls' => true,
    'showColorPicker' => false,
    'showThicknessPicker' => false,
    'defaultColor' => '#000000',
    'defaultThickness' => 1,
    'helpText' => 'Please sign in the box below',
    'id' => null,
])

@php
    $id = $id ?? 'signature-pad-' . Str::random(8);
    $hasError = $errors->has($name);
@endphp

<div {{ $attributes->merge(['class' => 'mb-4']) }}>
    <div class="signature-component" data-signature-pad-container>
        <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-1">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
        
        @if($helpText)
            <p class="text-sm text-gray-500 mb-2">{{ $helpText }}</p>
        @endif
        
        <div class="border rounded-md @if($hasError) border-red-500 @else border-gray-300 @endif">
            <div class="signature-pad-container bg-white rounded-md overflow-hidden">
                <canvas 
                    id="{{ $id }}"
                    class="signature-pad w-full"
                    data-height="{{ $height }}"
                    @if($width) width="{{ $width }}" @endif
                    data-default-color="{{ $defaultColor }}"
                    data-default-thickness="{{ $defaultThickness }}"
                ></canvas>
                <input type="hidden" id="{{ $name }}_input" name="{{ $name }}" value="{{ $value }}" @if($required) required="required" @endif>
            </div>
            
            @if($showControls)
                <div class="flex items-center justify-between p-2 bg-gray-50 border-t border-gray-200">
                    <div class="flex items-center space-x-2">
                        @if($showColorPicker)
                            <div class="flex items-center">
                                <label for="{{ $id }}-color" class="sr-only">Color</label>
                                <input type="color" id="{{ $id }}-color" value="{{ $defaultColor }}" 
                                    class="w-8 h-8 rounded cursor-pointer border border-gray-300" 
                                    data-color-picker>
                            </div>
                        @endif
                        
                        @if($showThicknessPicker)
                            <div class="flex items-center space-x-1">
                                <label for="{{ $id }}-thickness" class="text-xs text-gray-500">Width:</label>
                                <input type="range" id="{{ $id }}-thickness" min="0.5" max="5" step="0.5" 
                                    value="{{ $defaultThickness }}" 
                                    class="w-24" 
                                    data-thickness-picker>
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <button type="button" 
                            class="px-3 py-1.5 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            data-clear-button>
                            Clear
                        </button>
                        <button type="button" 
                            class="px-3 py-1.5 text-xs font-medium text-white bg-indigo-600 border border-transparent rounded-md shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            data-save-button>
                            Save
                        </button>
                    </div>
                </div>
            @endif
        </div>
        
        <div data-required-message class="mt-1 text-sm text-red-600 @if(!$hasError) hidden @endif">
            {{ $hasError ? $errors->first($name) : 'Signature is required' }}
        </div>
    </div>
</div>

@once
    @push('styles')
    <style>
        .signature-pad {
            touch-action: none;
        }
    </style>
    @endpush
    
    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check if SignaturePad library is available
            if (typeof SignaturePad === 'undefined') {
                // Load the library if not available
                const script = document.createElement('script');
                script.src = "https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js";
                script.onload = initializeSignaturePad;
                document.head.appendChild(script);
            } else {
                initializeSignaturePad();
            }
            
            function initializeSignaturePad() {
                const canvasElement = document.getElementById('{{ $id }}');
                if (!canvasElement) return;
                
                // Set canvas dimensions
                const height = canvasElement.getAttribute('data-height') || 200;
                canvasElement.height = height;
                
                // Initialize signature pad
                const signaturePad = new SignaturePad(canvasElement, {
                    backgroundColor: 'rgba(255, 255, 255, 0)',
                    penColor: canvasElement.getAttribute('data-default-color') || '#000000',
                    minWidth: 0.5,
                    maxWidth: 2.5
                });
                
                // Resize canvas
                resizeCanvas();
                window.addEventListener('resize', resizeCanvas);
                
                function resizeCanvas() {
                    const ratio = Math.max(window.devicePixelRatio || 1, 1);
                    const width = canvasElement.offsetWidth * ratio;
                    const height = canvasElement.height * ratio;
                    
                    canvasElement.width = width;
                    canvasElement.height = height;
                    canvasElement.getContext('2d').scale(ratio, ratio);
                    
                    // This will clear the canvas, so we need to redraw from the input value
                    if (document.getElementById('{{ $name }}_input').value) {
                        loadExistingSignature();
                    } else {
                        signaturePad.clear();
                    }
                }
                
                // Load existing signature if available
                function loadExistingSignature() {
                    const dataUrl = document.getElementById('{{ $name }}_input').value;
                    if (!dataUrl) return;
                    
                    const image = new Image();
                    image.onload = function() {
                        const ctx = canvasElement.getContext('2d');
                        ctx.drawImage(image, 0, 0, canvasElement.width, canvasElement.height);
                        signaturePad._isEmpty = false;
                    };
                    image.src = dataUrl;
                }
                
                if (document.getElementById('{{ $name }}_input').value) {
                    loadExistingSignature();
                }
                
                // Clear button
                const clearButton = canvasElement.closest('[data-signature-pad-container]').querySelector('[data-clear-button]');
                if (clearButton) {
                    clearButton.addEventListener('click', function() {
                        signaturePad.clear();
                        document.getElementById('{{ $name }}_input').value = '';
                        const requiredMessage = canvasElement.closest('[data-signature-pad-container]').querySelector('[data-required-message]');
                        if (requiredMessage) requiredMessage.classList.add('hidden');
                    });
                }
                
                // Save button
                const saveButton = canvasElement.closest('[data-signature-pad-container]').querySelector('[data-save-button]');
                if (saveButton) {
                    saveButton.addEventListener('click', function() {
                        if (signaturePad.isEmpty()) {
                            const requiredMessage = canvasElement.closest('[data-signature-pad-container]').querySelector('[data-required-message]');
                            if (requiredMessage && document.getElementById('{{ $name }}_input').hasAttribute('required')) {
                                requiredMessage.classList.remove('hidden');
                            }
                            return;
                        }
                        
                        const dataURL = signaturePad.toDataURL('image/png');
                        document.getElementById('{{ $name }}_input').value = dataURL;
                        
                        const requiredMessage = canvasElement.closest('[data-signature-pad-container]').querySelector('[data-required-message]');
                        if (requiredMessage) requiredMessage.classList.add('hidden');
                    });
                }
                
                // Color picker
                const colorPicker = canvasElement.closest('[data-signature-pad-container]').querySelector('[data-color-picker]');
                if (colorPicker) {
                    colorPicker.addEventListener('change', function() {
                        signaturePad.penColor = this.value;
                    });
                }
                
                // Thickness picker
                const thicknessPicker = canvasElement.closest('[data-signature-pad-container]').querySelector('[data-thickness-picker]');
                if (thicknessPicker) {
                    thicknessPicker.addEventListener('input', function() {
                        signaturePad.minWidth = parseFloat(this.value) * 0.5;
                        signaturePad.maxWidth = parseFloat(this.value) * 2.5;
                    });
                }
                
                // Form validation
                const form = canvasElement.closest('form');
                if (form && document.getElementById('{{ $name }}_input').hasAttribute('required')) {
                    form.addEventListener('submit', function(event) {
                        if (signaturePad.isEmpty() && !document.getElementById('{{ $name }}_input').value) {
                            event.preventDefault();
                            const requiredMessage = canvasElement.closest('[data-signature-pad-container]').querySelector('[data-required-message]');
                            if (requiredMessage) requiredMessage.classList.remove('hidden');
                            canvasElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    });
                }
            }
        });
    </script>
    @endpush
@endonce 