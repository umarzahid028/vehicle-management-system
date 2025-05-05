@extends('layouts.app')

@section('content')
<div class="py-8">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">
                <h1 class="text-2xl font-semibold text-gray-900 mb-6">Signature Pad Example</h1>
                
                <form action="{{ route('dashboard') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                    
                    <div>
                        <label for="agreement" class="block text-sm font-medium text-gray-700 mb-1">
                            Agreement Details
                        </label>
                        <textarea name="agreement" id="agreement" rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Enter agreement details here..."></textarea>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            Signature <span class="text-red-500">*</span>
                        </label>
                        
                        <x-signature-pad 
                            name="customer_signature"
                            required="true"
                            height="200"
                            requiredMessage="Please sign the document to continue" 
                        />
                        
                        <p class="mt-2 text-sm text-gray-500">
                            By signing above, you agree to the terms and conditions outlined in this document.
                        </p>
                    </div>
                    
                    <hr class="my-6 border-gray-200">
                    
                    <h2 class="text-xl font-semibold text-gray-800 mb-4">Additional Examples</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Compact signature pad with limited options -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Compact Signature</h3>
                            <x-signature-pad 
                                name="compact_signature"
                                height="150"
                                width="300"
                                showColorPicker="false"
                                showThicknessPicker="false"
                                clearButtonText="Reset"
                                saveButtonText="Done"
                            />
                        </div>
                        
                        <!-- Colorful signature pad with preset value -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-700 mb-2">Colorful Signature</h3>
                            <x-signature-pad 
                                name="colorful_signature"
                                colors="#000000,#e91e63,#2196f3,#4caf50,#ff9800"
                                thicknesses="2,4,6"
                                defaultColor="#2196f3"
                                defaultThickness="4"
                            />
                        </div>
                    </div>
                    
                    <div class="flex justify-end pt-6">
                        <button type="submit" 
                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Submit Form
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection 