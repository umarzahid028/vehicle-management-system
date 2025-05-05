@props([
    'name' => 'signature',
    'id' => 'signature-pad',
    'clearButtonId' => 'clearButton',
    'saveButtonId' => 'saveButton',
    'required' => false,
    'formId' => null,
    'containerClass' => '',
    'value' => '',
    'label' => 'Signature',
    'showSaveButton' => true,
])

<div class="signature-pad-container {{ $containerClass }}">
    <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 mb-1">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    
    <div class="border border-gray-300 rounded-md p-4 bg-white">
        <div class="canvas-container flex justify-center" style="width: 100%; min-height: 240px;">
            <canvas id="{{ $id }}" class="border border-gray-200 rounded-md bg-white w-full" style="height: 240px; touch-action: none;"></canvas>
        </div>
        
        <div class="flex justify-between items-center mt-3">
            <button type="button" id="{{ $clearButtonId }}" class="py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Clear
                </div>
            </button>
            
            @if($showSaveButton)
            <button type="button" id="{{ $saveButtonId }}" class="py-2 px-3 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 opacity-50 cursor-not-allowed">
                <div class="flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    Save Signature
                </div>
            </button>
            @endif
        </div>
    </div>
    
    <div id="{{ $id }}-error" class="mt-1 text-sm text-red-600"></div>
    
    <!-- Hidden input to store signature data -->
    <input type="hidden" name="{{ $name }}" id="{{ $name }}" value="{{ $value }}" @if($required) required @endif />
</div>

@once
    @push('scripts')
    <script src="{{ asset('js/signature_pad.umd.min.js') }}"></script>
    <script type="module">
        import SignaturePadComponent from '{{ asset('js/components/signature-pad.js') }}';

        document.addEventListener('DOMContentLoaded', () => {
            const padElement = document.getElementById('{{ $id }}');
            if (!padElement) return;
            
            const options = {
                canvasSelector: '#{{ $id }}',
                clearButtonSelector: '#{{ $clearButtonId }}',
                saveButtonSelector: '#{{ $saveButtonId }}',
                inputSelector: '#{{ $name }}',
                containerSelector: '.signature-pad-container',
                formSelector: '{{ $formId ? "#$formId" : "form" }}',
                showToolbar: true,
                padOptions: {
                    backgroundColor: "rgb(255, 255, 255)",
                    penColor: "rgb(0, 0, 100)"
                }
            };
            
            const signaturePad = new SignaturePadComponent(options);
            
            // If there's an existing signature, load it
            const existingSignature = document.getElementById('{{ $name }}').value;
            if (existingSignature) {
                const image = new Image();
                image.src = existingSignature;
                image.onload = () => {
                    const canvas = document.getElementById('{{ $id }}');
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(image, 0, 0, canvas.width, canvas.height);
                    
                    // Enable save button since we have a signature
                    const saveButton = document.getElementById('{{ $saveButtonId }}');
                    if (saveButton) {
                        saveButton.disabled = false;
                        saveButton.classList.remove('opacity-50', 'cursor-not-allowed');
                    }
                };
            }
            
            // Listen for save button click
            const saveButton = document.getElementById('{{ $saveButtonId }}');
            if (saveButton) {
                saveButton.addEventListener('click', () => {
                    if (signaturePad.isEmpty()) {
                        return;
                    }
                    
                    // Store signature data in input
                    document.getElementById('{{ $name }}').value = signaturePad.getSignatureData();
                    
                    // Visual feedback
                    const originalText = saveButton.innerHTML;
                    saveButton.innerHTML = '<div class="flex items-center"><svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Saved!</div>';
                    saveButton.disabled = true;
                    saveButton.classList.add('bg-green-600');
                    saveButton.classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    
                    setTimeout(() => {
                        saveButton.innerHTML = originalText;
                        saveButton.disabled = false;
                        saveButton.classList.remove('bg-green-600');
                        saveButton.classList.add('bg-blue-600', 'hover:bg-blue-700');
                    }, 1500);
                });
            }
        });
    </script>
    @endpush
@endonce 