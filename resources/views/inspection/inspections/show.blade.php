<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Inspection Details') }}: {{ $inspection->vehicle->year }} {{ $inspection->vehicle->make }} {{ $inspection->vehicle->model }}
                <span class="text-base font-normal text-gray-500">({{ $inspection->inspectionStage->name }})</span>
            </h2>
            <div class="flex space-x-2">
                @if($inspection->status !== 'completed')
                    <a href="{{ route('inspection.inspections.edit', $inspection) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <x-heroicon-o-clipboard-document-check class="h-4 w-4 mr-1" />
                        Continue Inspection
                    </a>
                @endif
                
                @if($inspection->status === 'completed' && auth()->user()->hasAnyRole(['Admin', 'Sales Manager', 'Recon Manager']))
                    @php
                        $needsRepairItems = $inspection->itemResults()
                            ->where('requires_repair', true)
                            ->where('repair_completed', false)
                            ->count();
                    @endphp
                    
                    @if($needsRepairItems === 0)
                        <form action="{{ route('inspection.inspections.assign-to-sales', $inspection) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                Assign to Sales Team
                            </button>
                        </form>
                    @else
                        <button disabled class="inline-flex items-center px-4 py-2 bg-gray-400 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest cursor-not-allowed">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Complete Repairs First
                        </button>
                    @endif
                @endif
                
                <a href="{{ route('inspection.inspections.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <x-heroicon-o-arrow-left class="h-4 w-4 mr-1" />
                    Back to Inspections
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="container mx-auto space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            @endif

            <!-- Inspection Overview Card -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Vehicle Information</h3>
                            <p class="text-gray-600">{{ $inspection->vehicle->year }} {{ $inspection->vehicle->make }} {{ $inspection->vehicle->model }}</p>
                            <p class="text-gray-600">VIN: {{ $inspection->vehicle->vin }}</p>
                            <p class="text-gray-600">Stock #: {{ $inspection->vehicle->stock_number }}</p>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                    $inspection->vehicle->status === 'arrived' ? 'bg-green-100 text-green-800' : 
                                    ($inspection->vehicle->status === 'delivered' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800') 
                                }}">
                                    Vehicle Status: {{ ucfirst($inspection->vehicle->status) }}
                                </span>
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('vehicles.show', $inspection->vehicle) }}" class="text-indigo-600 hover:text-indigo-900 text-sm">
                                    View Full Vehicle Details
                                </a>
                            </div>
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Inspection Details</h3>
                            <p class="text-gray-600">Stage: {{ $inspection->inspectionStage->name }}</p>
                            <p class="text-gray-600">Inspector: {{ $inspection->user->name }}</p>
                            <p class="text-gray-600">Date: {{ $inspection->inspection_date ? $inspection->inspection_date->format('M d, Y') : 'Not started' }}</p>
                            @if($inspection->completed_date)
                                <p class="text-gray-600">Completed: {{ $inspection->completed_date->format('M d, Y') }}</p>
                            @endif
                        </div>
                        
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Status</h3>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ 
                                $inspection->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                ($inspection->status === 'in_progress' ? 'bg-blue-100 text-blue-800' : 
                                ($inspection->status === 'failed' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800')) 
                            }}">
                                {{ ucfirst(str_replace('_', ' ', $inspection->status)) }}
                            </span>
                        </div>
                    </div>

                    @if($inspection->notes)
                        <div class="mt-4 border-t pt-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-1">Notes</h3>
                            <p class="text-gray-600">{{ $inspection->notes }}</p>
                        </div>
                    @endif

                    <div class="mt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Inspection Progress</h3>
                        <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                            @php
                                $totalItems = $inspection->itemResults->whereIn('status', ['completed', 'warning', 'fail'])->count();
                                $completedItems = $inspection->itemResults->whereIn('status', ['completed'])->count();
                                $progress = $totalItems > 0 ? ($completedItems / $totalItems) * 100 : 0;
                            @endphp
                            <div class="bg-blue-600 h-2.5 rounded-full" style="width: {{ $progress }}%"></div>
                        </div>
                        <p class="text-sm text-gray-600">{{ $completedItems }} of {{ $totalItems }} items assessed ({{ round($progress) }}%)</p>
                    </div>

                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Cost Summary</h3>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm text-gray-600">Estimated Total Cost:</span>
                                    <span class="text-lg font-semibold text-gray-900">${{ number_format($totalEstimatedCost, 2) }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Actual Total Cost:</span>
                                    <span class="text-lg font-semibold {{ $totalActualCost > $totalEstimatedCost ? 'text-red-600' : 'text-green-600' }}">
                                        ${{ number_format($totalActualCost, 2) }}
                                    </span>
                                </div>
                            </div>

                            <!-- Cost Difference -->
                            @if($totalActualCost > 0)
                                <div class="mt-2 text-sm">
                                    @php
                                        $difference = $totalActualCost - $totalEstimatedCost;
                                        $percentDiff = $totalEstimatedCost > 0 ? ($difference / $totalEstimatedCost) * 100 : 0;
                                    @endphp
                                    <span class="{{ $difference > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $difference > 0 ? 'Over' : 'Under' }} budget by: ${{ number_format(abs($difference), 2) }}
                                        ({{ number_format(abs($percentDiff), 1) }}%)
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inspection Items -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Inspection Items</h3>
                        <div class="text-sm text-gray-500">
                            Total Items: {{ $totalItems }}
                        </div>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Images</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($inspection->itemResults as $result)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $result->inspectionItem->name }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                                $result->status === 'pass' ? 'bg-green-100 text-green-800' : 
                                                ($result->status === 'warning' ? 'bg-yellow-100 text-yellow-800' : 
                                                ($result->status === 'fail' ? 'bg-red-100 text-red-800' : 'bg-gray-100 text-gray-800')) 
                                            }}">
                                            @if($result->status  == 'warning')
                                                Repair
                                            @elseif($result->status  == 'fail')
                                                Replace
                                            @else
                                                {{ $result->status  }}
                                            @endif
                                            </span>
                                            @if($result->repair_completed)
                                                <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    Repair Completed
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                {{ $result->assignedVendor ? $result->assignedVendor->name : 'Not Assigned' }} ({{ $result->assignedVendor->type->name ?? "N/A" }})
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                @if($result->cost > 0)
                                                    ${{ number_format($result->cost, 2) }}
                                                @else
                                                    -
                                                @endif
                                            </div>
                                            @if($result->actual_cost > 0)
                                                <div class="text-xs text-gray-500">
                                                    Actual: ${{ number_format($result->actual_cost, 2) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900">
                                                {{ $result->notes ?: '-' }}
                                            </div>
                                            @if($result->completion_notes)
                                                <div class="text-xs text-gray-500 mt-1">
                                                    <strong>Completion Notes:</strong> {{ $result->completion_notes }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($result->repairImages->count() > 0)
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach($result->repairImages as $image)
                                                        <a href="{{ Storage::url($image->image_path) }}" target="_blank" class="block">

                                                        <img src="{{ Storage::url($image->image_path) }}" alt="Repair Image" class="h-10 w-10 object-cover rounded">
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-sm text-gray-500">No images</span>
                                            @endif
                                            
                                          
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-gray-50">
                                <tr>
                                    <td colspan="3" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        Total Cost
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">
                                            Est: ${{ number_format($totalEstimatedCost, 2) }}
                                        </div>
                                        @if($totalActualCost > 0)
                                            <div class="text-xs text-gray-500">
                                                Act: ${{ number_format($totalActualCost, 2) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            @if($inspection->notes)
                <!-- Additional Notes -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Additional Notes</h3>
                        <div class="text-gray-600 whitespace-pre-line">{{ $inspection->notes }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex justify-center items-center p-4">
        <div class="bg-white rounded-lg max-w-3xl w-full max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-lg font-medium text-gray-900" id="modal-title">Repair Images</h3>
                <button type="button" class="text-gray-400 hover:text-gray-500" onclick="closeImagesModal()">
                    <x-heroicon-o-x-mark class="h-6 w-6" />
                </button>
            </div>
            <div class="p-4" id="modal-content">
                <!-- Images will be loaded here -->
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openImagesModal(itemId) {
            const modal = document.getElementById('imageModal');
            const content = document.getElementById('modal-content');
            const imagesContainer = document.getElementById('images-' + itemId);
            const title = document.getElementById('modal-title');
            
            // Get the item name from the table
            const itemName = document.querySelector(`[id="images-${itemId}"]`).closest('tr').querySelector('td:first-child .text-sm').textContent;
            title.textContent = `Images for ${itemName}`;
            
            // Clear previous content
            content.innerHTML = '';
            
            // Clone the images
            const images = imagesContainer.innerHTML;
            content.innerHTML = images;
            
            // Show modal
            modal.classList.remove('hidden');
            
            // Prevent scrolling on background
            document.body.style.overflow = 'hidden';
        }
        
        function closeImagesModal() {
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        
        // Close modal when clicking outside
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImagesModal();
            }
        });
    </script>
    @endpush
</x-app-layout> 