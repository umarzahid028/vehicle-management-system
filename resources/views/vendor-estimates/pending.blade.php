<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pending Vendor Estimates') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    @if($pendingEstimates->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-gray-500">No pending estimates to approve.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vehicle</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estimated Cost</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Submitted</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pendingEstimates as $estimate)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">
                                                    {{ $estimate->inspectionItemResult->vehicleInspection->vehicle->year }}
                                                    {{ $estimate->inspectionItemResult->vehicleInspection->vehicle->make }}
                                                    {{ $estimate->inspectionItemResult->vehicleInspection->vehicle->model }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    Stock: {{ $estimate->inspectionItemResult->vehicleInspection->vehicle->stock_number }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ $estimate->vendor->name }}</div>
                                                <div class="text-xs text-gray-500">
                                                    {{ $estimate->vendor->vendorType ? $estimate->vendor->vendorType->name : 'No Type' }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="text-sm text-gray-900">
                                                    {{ $estimate->inspectionItemResult->inspectionItem->name }}
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    {{ Str::limit($estimate->description, 40) }}
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">${{ number_format($estimate->estimated_cost, 2) }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-500">{{ $estimate->created_at->format('M d, Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ $estimate->created_at->format('g:i A') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <div class="flex space-x-2">
                                                    <form action="{{ route('vendor-estimates.approve', $estimate) }}" method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                                                    </form>
                                                    <span class="text-gray-300">|</span>
                                                    <button type="button" onclick="openRejectModal({{ $estimate->id }})" class="text-red-600 hover:text-red-900">Reject</button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg max-w-md w-full">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Reject Estimate</h3>
                <form id="rejectForm" action="" method="POST">
                    @csrf
                    @method('PATCH')
                    <div class="mb-4">
                        <label for="rejected_reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Rejection</label>
                        <textarea id="rejected_reason" name="rejected_reason" rows="3" required 
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeRejectModal()" 
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center px-4 py-2 border border-transparent rounded-md text-sm font-medium text-white bg-red-600 hover:bg-red-700">
                            Confirm Rejection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function openRejectModal(estimateId) {
            document.getElementById('rejectForm').action = `/vendor-estimates/${estimateId}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }
        
        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
        }
    </script>
</x-app-layout> 