@props(['vehicle'])

<div class="bg-white shadow overflow-hidden sm:rounded-lg p-4 mb-5">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-medium text-gray-900">Vehicle Status</h3>
        <div class="text-sm font-medium text-gray-500">Current Status: 
            <span class="px-2 py-1 rounded 
                @if (in_array($vehicle->status, [$vehicle::STATUS_SOLD, $vehicle::STATUS_ARCHIVE]))
                    bg-gray-500 text-white
                @elseif (in_array($vehicle->status, [$vehicle::STATUS_TRANSPORT_CANCELLED, $vehicle::STATUS_INSPECTION_CANCELLED, $vehicle::STATUS_REPAIR_CANCELLED]))
                    bg-red-500 text-white
                @elseif (in_array($vehicle->status, [$vehicle::STATUS_TRANSPORT_COMPLETED, $vehicle::STATUS_INSPECTION_COMPLETED, $vehicle::STATUS_REPAIR_COMPLETED, $vehicle::STATUS_GOODWILL_CLAIMS_COMPLETED]))
                    bg-green-500 text-white
                @elseif (in_array($vehicle->status, [$vehicle::STATUS_READY_FOR_SALE, $vehicle::STATUS_READY_FOR_SALE_ASSIGNED]))
                    bg-blue-500 text-white
                @elseif (in_array($vehicle->status, [$vehicle::STATUS_TRANSPORT_IN_PROGRESS, $vehicle::STATUS_INSPECTION_IN_PROGRESS, $vehicle::STATUS_REPAIR_IN_PROGRESS, $vehicle::STATUS_TRANSPORT_IN_TRANSIT]))
                    bg-yellow-500 text-white
                @elseif ($vehicle->status === $vehicle::STATUS_AVAILABLE)
                    bg-indigo-500 text-white
                @else
                    bg-purple-500 text-white
                @endif">
                {{ $vehicle->status }}
            </span>
        </div>
    </div>

    <div>
        <form id="vehicle-status-form" action="{{ route('vehicles.update-status', $vehicle) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">Update Status</label>
                    <select id="status" name="status" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">Select new status</option>
                        <!-- Status options will be loaded via JavaScript -->
                    </select>
                </div>

                <!-- Additional fields for specific status changes -->
                <div id="sales-team-section" class="hidden">
                    <label for="sales_team_id" class="block text-sm font-medium text-gray-700">Assign to Sales Team Member</label>
                    <select id="sales_team_id" name="sales_team_id" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                        <option value="">Select sales team member</option>
                        <!-- Sales team options will be loaded via AJAX if needed -->
                    </select>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea id="notes" name="notes" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                </div>
            </div>
            
            <div class="mt-4 flex justify-end">
                <button type="submit" class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const vehicleId = {{ $vehicle->id }};
        const statusSelect = document.getElementById('status');
        const salesTeamSection = document.getElementById('sales-team-section');
        const salesTeamSelect = document.getElementById('sales_team_id');
        
        // Load available status transitions
        fetch(`/vehicles/${vehicleId}/statuses`)
            .then(response => response.json())
            .then(data => {
                statusSelect.innerHTML = '<option value="">Select new status</option>';
                
                data.available_transitions.forEach(status => {
                    const option = document.createElement('option');
                    option.value = status;
                    option.textContent = status;
                    statusSelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading status options:', error));
            
        // Show/hide additional fields based on selected status
        statusSelect.addEventListener('change', function() {
            // Hide all additional sections first
            salesTeamSection.classList.add('hidden');
            
            // Show relevant sections based on selected status
            if (this.value === '{{ $vehicle::STATUS_READY_FOR_SALE_ASSIGNED }}') {
                salesTeamSection.classList.remove('hidden');
                
                // Load sales team members if needed
                if (salesTeamSelect.options.length <= 1) {
                    fetch('/admin/users?role=sales')
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(user => {
                                const option = document.createElement('option');
                                option.value = user.id;
                                option.textContent = user.name;
                                salesTeamSelect.appendChild(option);
                            });
                        })
                        .catch(error => console.error('Error loading sales team members:', error));
                }
            }
        });
    });
</script>
@endpush 