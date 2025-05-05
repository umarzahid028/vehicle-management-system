<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Icon Input Examples') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-semibold mb-4">Input Components with Icons</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            <!-- Full Name -->
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                                <x-shadcn.icon-input
                                    name="full_name"
                                    id="full_name"
                                    placeholder="Enter team member's full name"
                                    :icon="'<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-5 w-5 text-gray-400\' viewBox=\'0 0 20 20\' fill=\'currentColor\'><path fill-rule=\'evenodd\' d=\'M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z\' clip-rule=\'evenodd\' /></svg>'"
                                />
                            </div>
                            
                            <!-- Position -->
                            <div>
                                <label for="position" class="block text-sm font-medium text-gray-700 mb-1">Position</label>
                                <x-shadcn.icon-input
                                    name="position"
                                    id="position"
                                    placeholder="e.g. Sales Representative"
                                    :icon="'<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-5 w-5 text-gray-400\' viewBox=\'0 0 20 20\' fill=\'currentColor\'><path fill-rule=\'evenodd\' d=\'M6 6V5a3 3 0 013-3h2a3 3 0 013 3v1h2a2 2 0 012 2v3.57A22.952 22.952 0 0110 13a22.95 22.95 0 01-8-1.43V8a2 2 0 012-2h2zm2-1a1 1 0 011-1h2a1 1 0 011 1v1H8V5zm1 5a1 1 0 011-1h.01a1 1 0 110 2H10a1 1 0 01-1-1z\' clip-rule=\'evenodd\' /></svg>'"
                                />
                            </div>
                            
                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                <x-shadcn.icon-input
                                    type="email"
                                    name="email"
                                    id="email"
                                    placeholder="Enter email address"
                                    :icon="'<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-5 w-5 text-gray-400\' viewBox=\'0 0 20 20\' fill=\'currentColor\'><path d=\'M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z\' /><path d=\'M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z\' /></svg>'"
                                />
                            </div>
                            
                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                <x-shadcn.icon-input
                                    type="tel"
                                    name="phone"
                                    id="phone"
                                    placeholder="Enter phone number"
                                    :icon="'<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-5 w-5 text-gray-400\' viewBox=\'0 0 20 20\' fill=\'currentColor\'><path d=\'M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z\' /></svg>'"
                                />
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <!-- Address -->
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                                <x-shadcn.icon-input
                                    name="address"
                                    id="address"
                                    placeholder="Enter street address"
                                    :icon="'<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-5 w-5 text-gray-400\' viewBox=\'0 0 20 20\' fill=\'currentColor\'><path fill-rule=\'evenodd\' d=\'M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z\' clip-rule=\'evenodd\' /></svg>'"
                                />
                            </div>
                            
                            <!-- Date -->
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                                <x-shadcn.icon-input
                                    type="date"
                                    name="date"
                                    id="date"
                                    :icon="'<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-5 w-5 text-gray-400\' viewBox=\'0 0 20 20\' fill=\'currentColor\'><path fill-rule=\'evenodd\' d=\'M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z\' clip-rule=\'evenodd\' /></svg>'"
                                />
                            </div>
                            
                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                                <x-shadcn.icon-input
                                    type="password"
                                    name="password"
                                    id="password"
                                    placeholder="Enter password"
                                    :icon="'<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-5 w-5 text-gray-400\' viewBox=\'0 0 20 20\' fill=\'currentColor\'><path fill-rule=\'evenodd\' d=\'M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z\' clip-rule=\'evenodd\' /></svg>'"
                                />
                            </div>
                            
                            <!-- Amount -->
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                                <x-shadcn.icon-input
                                    type="number"
                                    name="amount"
                                    id="amount"
                                    placeholder="Enter amount"
                                    :icon="'<svg xmlns=\'http://www.w3.org/2000/svg\' class=\'h-5 w-5 text-gray-400\' viewBox=\'0 0 20 20\' fill=\'currentColor\'><path d=\'M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z\' /><path fill-rule=\'evenodd\' d=\'M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z\' clip-rule=\'evenodd\' /></svg>'"
                                />
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <x-shadcn.button type="button">
                            Save Information
                        </x-shadcn.button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 