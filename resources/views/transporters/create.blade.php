<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add New Transporter') }}
            </h2>
            <div>
                <a href="{{ route('transporters.index') }}">
                    <x-shadcn.button variant="outline">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('Back to List') }}
                    </x-shadcn.button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if ($errors->any())
                        <div class="mb-4 p-4 bg-red-100 border border-red-200 text-red-700 rounded">
                            <ul class="list-disc pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form action="{{ route('transporters.store') }}" method="POST">
                        @csrf
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Basic Information -->
                            <div class="bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Basic Information</h3>
                                
                                <!-- Company Name -->
                                <div class="mb-4">
                                    <label for="name" class="block text-sm font-medium text-gray-700">Company Name *</label>
                                    <div class="relative mt-1">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v4H7V5zm8 8v2h1v1H4v-1h1v-2a1 1 0 011-1h8a1 1 0 011 1z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <x-shadcn.input
                                            type="text"
                                            name="name"
                                            id="name"
                                            class="pl-10"
                                            :value="old('name')"
                                            required
                                        />
                                    </div>
                                </div>
                                
                                <!-- Contact Person -->
                                <div class="mb-4">
                                    <label for="contact_person" class="block text-sm font-medium text-gray-700">Contact Person</label>
                                    <div class="relative mt-1">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <x-shadcn.input
                                            type="text"
                                            name="contact_person"
                                            id="contact_person"
                                            class="pl-10"
                                            :value="old('contact_person')"
                                        />
                                    </div>
                                </div>
                                
                                <!-- Phone -->
                                <div class="mb-4">
                                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                                    <div class="relative mt-1">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" />
                                            </svg>
                                        </div>
                                        <x-shadcn.input
                                            type="text"
                                            name="phone"
                                            id="phone"
                                            class="pl-10"
                                            :value="old('phone')"
                                        />
                                    </div>
                                </div>
                                
                                <!-- Email -->
                                <div class="mb-4">
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                    <div class="relative mt-1">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                            </svg>
                                        </div>
                                        <x-shadcn.input
                                            type="email"
                                            name="email"
                                            id="email"
                                            class="pl-10"
                                            :value="old('email')"
                                        />
                                    </div>
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                                
                                <!-- Password -->
                                <div class="space-y-2">
                                    <label for="password" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Password</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <input
                                            id="password"
                                            class="flex h-9 w-full rounded-md border border-input bg-transparent pl-10 px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                            type="password"
                                            name="password"
                                            required
                                        />
                                    </div>
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>

                                <!-- Confirm Password -->
                                <div class="space-y-2">
                                    <label for="password_confirmation" class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70">Confirm Password</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <input
                                            id="password_confirmation"
                                            class="flex h-9 w-full rounded-md border border-input bg-transparent pl-10 px-3 py-1 text-sm shadow-sm transition-colors file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                            type="password"
                                            name="password_confirmation"
                                            required
                                        />
                                    </div>
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                                </div>
                                
                                <!-- Status -->
                                <div class="mb-4 flex items-center">
                                    <input type="checkbox" id="is_active" name="is_active" value="1" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                                    <label for="is_active" class="ml-2 block text-sm text-gray-900">Active</label>
                                </div>
                            </div>
                            
                            <!-- Address & Additional Information -->
                            <div class="bg-white p-6 rounded-lg shadow-md">
                                <h3 class="text-lg font-semibold mb-4 pb-2 border-b">Address & Additional Information</h3>
                                
                                <!-- Address -->
                                <div class="mb-4">
                                    <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                                    <div class="relative mt-1">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <x-shadcn.input
                                            type="text"
                                            name="address"
                                            id="address"
                                            class="pl-10"
                                            :value="old('address')"
                                        />
                                    </div>
                                </div>
                                
                                <!-- City, State, Zip in a row -->
                                <div class="grid grid-cols-3 gap-4 mb-4">
                                    <div>
                                        <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                                        <div class="relative mt-1">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z" />
                                                </svg>
                                            </div>
                                            <x-shadcn.input
                                                type="text"
                                                name="city"
                                                id="city"
                                                class="pl-10"
                                                :value="old('city')"
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <label for="state" class="block text-sm font-medium text-gray-700">State</label>
                                        <div class="relative mt-1">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                                                    <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <x-shadcn.input
                                                type="text"
                                                name="state"
                                                id="state"
                                                class="pl-10"
                                                :value="old('state')"
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <label for="zip" class="block text-sm font-medium text-gray-700">Zip</label>
                                        <div class="relative mt-1">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <x-shadcn.input
                                                type="text"
                                                name="zip"
                                                id="zip"
                                                class="pl-10"
                                                :value="old('zip')"
                                            />
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Notes -->
                                <div class="mb-4">
                                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                                    <div class="relative mt-1">
                                        <div class="absolute top-3 left-3 flex items-center pointer-events-none">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <textarea name="notes" id="notes" rows="4" class="mt-1 pl-10 block w-full border border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">{{ old('notes') }}</textarea>
                                    </div>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="mt-6 flex justify-end">
                                    <x-shadcn.button type="submit" variant="default">
                                        Create Transporter
                                    </x-shadcn.button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 