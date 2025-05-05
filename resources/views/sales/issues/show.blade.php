<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Sales Issue Details
            </h2>
            <div class="flex space-x-4">
                @if($issue->status === 'pending')
                    <form action="{{ route('sales.issues.update-status', $issue) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="reviewed">
                        <x-button>
                            Mark as Reviewed
                        </x-button>
                    </form>
                @endif
                <a href="{{ route('sales.issues.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Vehicle Information -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Vehicle Information</h3>
                            <div class="space-y-2">
                                <p><span class="font-medium">Stock Number:</span> {{ $issue->vehicle->stock_number }}</p>
                                <p><span class="font-medium">Vehicle:</span> {{ $issue->vehicle->year }} {{ $issue->vehicle->make }} {{ $issue->vehicle->model }}</p>
                                <p><span class="font-medium">VIN:</span> {{ $issue->vehicle->vin }}</p>
                            </div>
                        </div>

                        <!-- Issue Information -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Issue Details</h3>
                            <div class="space-y-2">
                                <p><span class="font-medium">Issue Type:</span> {{ $issue->issue_type }}</p>
                                <p><span class="font-medium">Priority:</span> 
                                    <span class="px-2 py-1 rounded text-sm 
                                        @if($issue->priority === 'high') bg-red-100 text-red-800
                                        @elseif($issue->priority === 'medium') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst($issue->priority) }}
                                    </span>
                                </p>
                                <p><span class="font-medium">Status:</span>
                                    <span class="px-2 py-1 rounded text-sm
                                        @if($issue->status === 'pending') bg-yellow-100 text-yellow-800
                                        @else bg-green-100 text-green-800
                                        @endif">
                                        {{ ucfirst($issue->status) }}
                                    </span>
                                </p>
                                <p><span class="font-medium">Reported By:</span> {{ $issue->reportedBy->name }}</p>
                                <p><span class="font-medium">Reported At:</span> {{ $issue->created_at->format('M d, Y H:i A') }}</p>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4">Description</h3>
                            <p class="whitespace-pre-wrap">{{ $issue->description }}</p>
                        </div>

                        @if($issue->reviewed_at)
                        <!-- Review Information -->
                        <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4">Review Information</h3>
                            <div class="space-y-2">
                                <p><span class="font-medium">Reviewed By:</span> {{ $issue->reviewedBy->name }}</p>
                                <p><span class="font-medium">Reviewed At:</span> {{ $issue->reviewed_at->format('M d, Y H:i A') }}</p>
                                @if($issue->review_notes)
                                    <p><span class="font-medium">Review Notes:</span></p>
                                    <p class="whitespace-pre-wrap">{{ $issue->review_notes }}</p>
                                @endif
                            </div>
                        </div>
                        @endif

                        @if($issue->goodwillClaim)
                        <!-- Related Goodwill Claim -->
                        <div class="bg-gray-50 p-4 rounded-lg md:col-span-2">
                            <h3 class="text-lg font-semibold mb-4">Related Goodwill Claim</h3>
                            <div class="space-y-2">
                                <p><span class="font-medium">Claim Status:</span> 
                                    <span class="px-2 py-1 rounded text-sm
                                        @if($issue->goodwillClaim->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($issue->goodwillClaim->status === 'approved') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($issue->goodwillClaim->status) }}
                                    </span>
                                </p>
                                <a href="{{ route('sales.goodwill-claims.show', $issue->goodwillClaim) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                                    View Claim Details
                                </a>
                            </div>
                        </div>
                        @else
                        <!-- Create Goodwill Claim Button -->
                        <div class="md:col-span-2">
                            <a href="{{ route('sales.goodwill-claims.create', ['vehicle_id' => $issue->vehicle_id, 'sales_issue_id' => $issue->id]) }}" 
                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                                Create Goodwill Claim
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 