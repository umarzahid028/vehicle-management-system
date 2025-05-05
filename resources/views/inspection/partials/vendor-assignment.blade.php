<div class="bg-white rounded-lg shadow p-4 mt-4">
    <h3 class="text-lg font-medium text-gray-900 mb-2">Vendor Assignment</h3>
    
    <form action="{{ route('inspection.results.assign-vendor', $result) }}" method="POST">
        @csrf
        @method('PATCH')
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label for="vendor_id" class="block text-sm font-medium text-gray-700 mb-1">Select Vendor</label>
                <select id="vendor_id" name="vendor_id" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">-- Select Vendor --</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}" {{ $result->vendor_id == $vendor->id ? 'selected' : '' }} 
                                data-is-onsite="{{ $vendor->vendorType && $vendor->vendorType->is_on_site ? '1' : '0' }}">
                            {{ $vendor->name }} 
                            @if($vendor->vendorType)
                                ({{ $vendor->vendorType->is_on_site ? 'On-Site' : 'Off-Site' }})
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
            
            <div>
                <label for="diagnostic_status" class="block text-sm font-medium text-gray-700 mb-1">Diagnostic Status</label>
                <select id="diagnostic_status" name="diagnostic_status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">None</option>
                    <option value="pending_diagnosis" {{ $result->diagnostic_status == 'pending_diagnosis' ? 'selected' : '' }}>Pending Diagnosis</option>
                    <option value="diagnosis_in_progress" {{ $result->diagnostic_status == 'diagnosis_in_progress' ? 'selected' : '' }}>Diagnosis In Progress</option>
                    <option value="diagnosis_completed" {{ $result->diagnostic_status == 'diagnosis_completed' ? 'selected' : '' }}>Diagnosis Completed</option>
                </select>
            </div>
            
            <div class="flex items-center">
                <input id="is_vendor_visible" name="is_vendor_visible" type="checkbox" class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                       {{ $result->is_vendor_visible ? 'checked' : '' }} value="1">
                <label for="is_vendor_visible" class="ml-2 block text-sm text-gray-900">Make visible to vendor</label>
            </div>
            
            <div>
                <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z" />
                    </svg>
                    {{ $result->vendor_id ? 'Update Assignment' : 'Assign Vendor' }}
                </button>
            </div>
        </div>
    </form>
    
    <!-- Vendor-specific information -->
    @if($result->vendor_id)
        <div class="mt-4 pt-4 border-t border-gray-200">
            <div class="flex justify-between items-center mb-2">
                <h4 class="text-md font-medium text-gray-900">Vendor Details</h4>
                <span class="text-sm text-gray-500">
                    @if($result->assigned_at)
                        Assigned: {{ $result->assigned_at->format('M d, Y g:i A') }}
                    @endif
                </span>
            </div>
            
            <div class="text-sm text-gray-600">
                <p>{{ $result->assignedVendor->name }}</p>
                <p>{{ $result->assignedVendor->contact_person }}</p>
                <p>{{ $result->assignedVendor->phone }}</p>
            </div>
            
            <!-- Offsite vendor estimate form -->
            @if($result->assignedVendor && $result->assignedVendor->vendorType && !$result->assignedVendor->vendorType->is_on_site && !$result->isPendingEstimate())
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <h4 class="text-md font-medium text-gray-900 mb-2">Request Estimate</h4>
                    <form action="{{ route('vendor-estimates.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="inspection_item_result_id" value="{{ $result->id }}">
                        <input type="hidden" name="vendor_id" value="{{ $result->vendor_id }}">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="estimated_cost" class="block text-sm font-medium text-gray-700 mb-1">Estimated Cost ($)</label>
                                <input type="number" step="0.01" min="0" id="estimated_cost" name="estimated_cost" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <textarea id="description" name="description" rows="2" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            </div>
                            
                            <div class="md:col-span-2">
                                <button type="submit" class="w-full md:w-auto inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" />
                                    </svg>
                                    Submit Estimate for Approval
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @endif
            
            <!-- Pending estimate display -->
            @foreach($result->vendorEstimates()->where('status', 'pending')->get() as $estimate)
                <div class="mt-4 p-4 bg-yellow-50 rounded-md border border-yellow-200">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="text-md font-medium text-yellow-800">Pending Estimate</h4>
                        <span class="text-sm text-yellow-600">Submitted: {{ $estimate->created_at->format('M d, Y') }}</span>
                    </div>
                    <p class="text-sm text-yellow-700 mb-2"><strong>Estimated Cost:</strong> ${{ number_format($estimate->estimated_cost, 2) }}</p>
                    <p class="text-sm text-yellow-700 mb-4">{{ $estimate->description }}</p>
                    
                    <!-- For managers to approve/reject -->
                    @can('approve-estimates')
                    <div class="flex space-x-2">
                        <form action="{{ route('vendor-estimates.approve', $estimate) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-green-100 text-green-700 rounded-md text-sm font-medium hover:bg-green-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                                Approve
                            </button>
                        </form>
                        
                        <button type="button" onclick="document.getElementById('reject-form-{{ $estimate->id }}').classList.toggle('hidden')" 
                                class="inline-flex items-center px-3 py-1 bg-red-100 text-red-700 rounded-md text-sm font-medium hover:bg-red-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                            Reject
                        </button>
                    </div>
                    
                    <form id="reject-form-{{ $estimate->id }}" action="{{ route('vendor-estimates.reject', $estimate) }}" method="POST" class="mt-2 hidden">
                        @csrf
                        @method('PATCH')
                        <div class="mb-2">
                            <label for="rejected_reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Rejection</label>
                            <textarea id="rejected_reason" name="rejected_reason" rows="2" required 
                                      class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>
                        <button type="submit" class="inline-flex items-center px-3 py-1 bg-red-600 text-white rounded-md text-sm font-medium hover:bg-red-700">
                            Confirm Rejection
                        </button>
                    </form>
                    @endcan
                </div>
            @endforeach
            
            <!-- Approved estimates display -->
            @foreach($result->vendorEstimates()->where('status', 'approved')->get() as $estimate)
                <div class="mt-4 p-4 bg-green-50 rounded-md border border-green-200">
                    <div class="flex justify-between items-center mb-2">
                        <h4 class="text-md font-medium text-green-800">Approved Estimate</h4>
                        <span class="text-sm text-green-600">Approved: {{ $estimate->approved_at->format('M d, Y') }}</span>
                    </div>
                    <p class="text-sm text-green-700"><strong>Cost:</strong> ${{ number_format($estimate->estimated_cost, 2) }}</p>
                    <p class="text-sm text-green-700">{{ $estimate->description }}</p>
                    <p class="text-sm text-green-600 mt-1">Approved by: {{ $estimate->approvedBy->name }}</p>
                </div>
            @endforeach
        </div>
    @endif
</div> 