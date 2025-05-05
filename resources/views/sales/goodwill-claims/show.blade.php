<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-2">
               
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Goodwill Claim Details
                </h2>
                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium 
                    @if($claim->status === 'pending') bg-yellow-50 text-yellow-800 ring-1 ring-inset ring-yellow-600/20
                    @elseif($claim->status === 'approved') bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20
                    @else bg-red-50 text-red-700 ring-1 ring-inset ring-red-600/20
                    @endif">
                    {{ ucfirst($claim->status) }}
                </span>
            </div>
            
            @if($claim->status === 'pending')
                <form action="{{ route('sales.goodwill-claims.update-status', $claim->id) }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                    @csrf
                    @method('PATCH')
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <span class="text-gray-500 text-sm">$</span>
                        </div>
                        <x-text-input type="number" step="0.01" name="estimated_cost" placeholder="Estimated Cost" required
                            class="pl-7 w-full sm:w-32" value="{{ old('estimated_cost', $claim->estimated_cost) }}" />
                        <x-input-error :messages="$errors->get('estimated_cost')" class="mt-2" />
                    </div>
                    <div class="flex gap-3">
                        <button type="submit" name="status" value="approved" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-medium text-white hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5"><path d="M20 6 9 17l-5-5"/></svg>
                            Approve
                        </button>
                        <button type="submit" name="status" value="rejected" class="flex-1 inline-flex items-center justify-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-medium text-white hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                            Reject
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </x-slot>

    <div class="py-6">
        <div class="container mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Main Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Overview Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-200 bg-gray-50">
                            <div class="px-6 py-3">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Claim Overview</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Reference ID</dt>
                                    <dd class="mt-1 text-sm text-gray-900">#{{ $claim->id }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created At</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $claim->created_at ? $claim->created_at->format('M d, Y H:i A') : 'N/A' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created By</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $claim->createdBy ? $claim->createdBy->name : 'Unknown' }}</dd>
                                </div>
                                @if($claim->status !== 'pending')
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Decision Date</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $claim->approved_at ? $claim->approved_at->format('M d, Y H:i A') : 'N/A' }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Decision By</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ $claim->approvedBy ? $claim->approvedBy->name : 'Unknown' }}</dd>
                                    </div>
                                @endif
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Estimated Cost</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $claim->estimated_cost ? '$'.number_format($claim->estimated_cost, 2) : 'N/A' }}</dd>
                                </div>
                                @if($claim->actual_cost)
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Actual Cost</dt>
                                        <dd class="mt-1 text-sm text-gray-900">${{ number_format($claim->actual_cost, 2) }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <!-- Issue Description Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-200 bg-gray-50">
                            <div class="px-6 py-3">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Issue Description</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="prose max-w-none">
                                <p class="whitespace-pre-wrap text-gray-700">{{ $claim->issue_description }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Requested Resolution Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-200 bg-gray-50">
                            <div class="px-6 py-3">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Requested Resolution</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="prose max-w-none">
                                <p class="whitespace-pre-wrap text-gray-700">{{ $claim->requested_resolution }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Decision Notes (if applicable) -->
                    @if($claim->status !== 'pending' && $claim->approval_notes)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="border-b border-gray-200 bg-gray-50">
                                <div class="px-6 py-3">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">Decision Notes</h3>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="prose max-w-none">
                                    <p class="whitespace-pre-wrap text-gray-700">{{ $claim->approval_notes }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column: Sidebar Information -->
                <div class="space-y-6">
                    <!-- Vehicle Information Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-200 bg-gray-50">
                            <div class="px-6 py-3">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Vehicle Information</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            @if($claim->vehicle)
                                <div class="flex flex-col space-y-3">
                                    <div class="flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M8 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM15 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                            <path d="M3 4a1 1 0 00-1 1v10a1 1 0 001 1h1.05a2.5 2.5 0 014.9 0H10a1 1 0 001-1v-1h3a1 1 0 00.8-.4l3-4a1 1 0 00.2-.6V8a1 1 0 00-1-1h-3.4a1 1 0 00-.8.4L11.25 10H7V5a1 1 0 00-1-1H3z" />
                                        </svg>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $claim->vehicle->year }} {{ $claim->vehicle->make }} {{ $claim->vehicle->model }}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Stock #:</p>
                                        <p class="text-sm font-medium">{{ $claim->vehicle->stock_number }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">VIN:</p>
                                        <p class="text-xs font-mono">{{ $claim->vehicle->vin }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center justify-center p-4 text-red-600 bg-red-50 rounded-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                    <p class="text-sm font-medium">Vehicle information not available</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Customer Information Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="border-b border-gray-200 bg-gray-50">
                            <div class="px-6 py-3">
                                <h3 class="text-lg font-medium leading-6 text-gray-900">Customer Information</h3>
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex flex-col space-y-3">
                                <div>
                                    <p class="text-sm text-gray-500">Name:</p>
                                    <p class="text-sm font-medium">{{ $claim->customer_name }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Phone:</p>
                                    <p class="text-sm font-medium">{{ $claim->customer_phone }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Email:</p>
                                    <p class="text-sm font-medium">{{ $claim->customer_email }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Customer Consent:</p>
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $claim->customer_consent ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $claim->customer_consent ? 'Given' : 'Not Given' }}
                                    </span>
                                    @if($claim->customer_consent && $claim->customer_consent_date)
                                        <p class="text-xs text-gray-500 mt-1">Given on {{ $claim->customer_consent_date->format('M d, Y') }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Customer Signature -->
                            <div class="mt-5 pt-5 border-t border-gray-200">
                                <p class="text-sm font-medium text-gray-700 mb-2">Signature Status</p>
                                
                                @if($claim->hasSignature())
                                    <div class="rounded-md border border-gray-200 p-3 bg-gray-50">
                                        <p class="text-xs text-gray-500 mb-2">Captured on {{ $claim->signature_date->format('M d, Y') }}</p>
                                        <div class="border border-gray-200 bg-white rounded-md p-2">
                                            <img src="data:image/png;base64,{{ $claim->customer_signature }}" 
                                                alt="Customer Signature" 
                                                class="max-h-16 max-w-full mx-auto" />
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ route('sales.goodwill-claims.signature.show',  $claim->id.'/signature') }}" 
                                       class="inline-flex w-full justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5">
                                            <path d="M20 6H4"></path><path d="M14 6V4a2 2 0 0 0-2-2H8a2 2 0 0 0-2 2v2"></path>
                                            <path d="M8 11h8"></path><path d="M8 16h8"></path><path d="M12 6v13"></path>
                                        </svg>
                                        Capture Signature
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Related Sales Issue (if any) -->
                    @if($claim->salesIssue)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="border-b border-gray-200 bg-gray-50">
                                <div class="px-6 py-3">
                                    <h3 class="text-lg font-medium leading-6 text-gray-900">Related Sales Issue</h3>
                                </div>
                            </div>
                            <div class="p-6">
                                <div class="flex flex-col space-y-3">
                                    <div>
                                        <p class="text-sm text-gray-500">Issue Type:</p>
                                        <p class="text-sm font-medium">{{ $claim->salesIssue->issue_type }}</p>
                                    </div>
                                    <div>
                                        <p class="text-sm text-gray-500">Status:</p>
                                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                            @if($claim->salesIssue->status === 'pending') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800
                                            @endif">
                                            {{ ucfirst($claim->salesIssue->status) }}
                                        </span>
                                    </div>
                                    <div class="pt-2">
                                        <a href="{{ route('sales.issues.show', $claim->salesIssue) }}" 
                                           class="inline-flex w-full justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5">
                                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                            View Issue Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('error'))
        <div class="fixed bottom-4 right-4 max-w-md bg-red-50 border-l-4 border-red-500 text-red-800 p-4 rounded shadow-lg" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif
    
    @if(session('success'))
        <div class="fixed bottom-4 right-4 max-w-md bg-green-50 border-l-4 border-green-500 text-green-800 p-4 rounded shadow-lg" role="alert">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif
</x-app-layout> 