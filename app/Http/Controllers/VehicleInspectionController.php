<?php

namespace App\Http\Controllers;

use App\Models\InspectionStage;
use App\Models\Vehicle;
use App\Models\VehicleInspection;
use App\Models\Vendor;
use App\Models\InspectionItem;
use App\Models\InspectionItemResult;
use App\Models\RepairImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VehicleInspectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
       // $this->middleware('role_or_permission:Admin|Sales Manager|Recon Manager|view vehicles');
    }

    /**
     * Display a listing of the vehicle inspections.
     */
    public function index(Request $request)
    {
       
        $query = VehicleInspection::with([
            'vehicle.vehicleInspections.inspectionStage',
            'inspectionStage',
            'itemResults.inspectionItem',
            'itemResults.repairImages',
            'itemResults.assignedVendor'
        ])
        ->select([
            'vehicle_inspections.*',
            'vehicles.stock_number',
            'vehicles.year',
            'vehicles.make',
            'vehicles.model',
            'vehicles.vin'
        ])
        ->join('vehicles', 'vehicle_inspections.vehicle_id', '=', 'vehicles.id')
        ->whereIn('vehicle_inspections.id', function($query) {
            $query->selectRaw('MAX(vi2.id)')
                ->from('vehicle_inspections as vi2')
                ->groupBy('vi2.vehicle_id');
        });

        // Apply search filter
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('vehicles.stock_number', 'LIKE', "%{$search}%")
                  ->orWhere('vehicles.vin', 'LIKE', "%{$search}%")
                  ->orWhere('vehicles.make', 'LIKE', "%{$search}%")
                  ->orWhere('vehicles.model', 'LIKE', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->has('status') && $request->status !== '') {
            $query->where('vehicle_inspections.status', $request->status);
        }

        // Get paginated results
        $inspections = $query->latest('vehicle_inspections.created_at')->paginate(10)->withQueryString();
        
        return view('inspection.inspections.index', compact('inspections'));
    }

    /**
     * Show the form for creating a new vehicle inspection.
     */
    public function create(Request $request)
    {
        $vehicleId = $request->query('vehicle_id');
        
        // Get vehicles that haven't been inspected yet
        $vehicles = Vehicle::whereDoesntHave('vehicleInspections')
            ->whereHas('transports', function($query) {
                $query->where('status', 'delivered');
            })
            ->orderBy('stock_number')
            ->get(['id', 'stock_number', 'year', 'make', 'model'])
            ->mapWithKeys(function($vehicle) {
                return [
                    $vehicle->id => $vehicle->year . ' ' . $vehicle->make . ' ' . $vehicle->model . ' (Stock #' . $vehicle->stock_number . ')'
                ];
            });
        
        // Add a highlighted prompt about comprehensive inspection
        session()->flash('info', 'Select a vehicle to begin the comprehensive inspection process.');
        
        return view('inspection.inspections.create', compact('vehicles', 'vehicleId'));
    }

    /**
     * Redirect legacy inspection creation to comprehensive view.
     * This method is preserved for backwards compatibility with any existing references.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        $vehicle = Vehicle::findOrFail($validated['vehicle_id']);
        
        // Redirect to comprehensive inspection
        return redirect()->route('comprehensive.show', $vehicle)
            ->with('info', 'Using comprehensive inspection system for better workflow.');
    }

    /**
     * Display the specified vehicle inspection.
     */
    public function show(VehicleInspection $inspection)
    {
        $inspection->load([
            'vehicle', 
            'inspectionStage.inspectionItems', 
            'user', 
            'vendor', 
            'itemResults.inspectionItem', 
            'itemResults.repairImages',
            'itemResults.assignedVendor'
        ]);
        
        // Create missing item results for all inspection items in this stage
        $existingItemIds = $inspection->itemResults->pluck('inspection_item_id')->toArray();
        $stageItems = $inspection->inspectionStage->inspectionItems;
        
        foreach ($stageItems as $item) {
            if (!in_array($item->id, $existingItemIds)) {
                $inspection->itemResults()->create([
                    'inspection_item_id' => $item->id,
                    'status' => 'pending',
                    'cost' => 0,
                    'actual_cost' => 0
                ]);
            }
        }
        
        // Reload the inspection with the new item results
        $inspection->load('itemResults.inspectionItem');
        
        // Calculate total costs
        $totalEstimatedCost = $inspection->itemResults->sum('cost');
        $totalActualCost = $inspection->itemResults->sum('actual_cost');
        
        // Update the inspection's total cost
        $inspection->update([
            'total_cost' => $totalEstimatedCost
        ]);
        
        $vendors = Vendor::orderBy('name')->pluck('name', 'id');
        
        return view('inspection.inspections.show', compact(
            'inspection', 
            'vendors',
            'totalEstimatedCost',
            'totalActualCost'
        ));
    }

    /**
     * Show the form for editing the specified vehicle inspection.
     */
    public function edit(VehicleInspection $inspection)
    {
        $vehicle = $inspection->vehicle;
        
        // Load stages with active inspection items
        $stages = InspectionStage::with(['inspectionItems' => function ($query) {
            $query->where('is_active', true)->orderBy('order');
        }])->where('is_active', true)->orderBy('order')->get();
        
        // Load active vendors
        $vendors = Vendor::where('is_active', true)->orderBy('name')->get();
        
        // Pass the inspection as existingInspection to match comprehensive view expectations
        $existingInspection = $inspection->load([
            'vehicle',
            'itemResults.inspectionItem',
            'itemResults.assignedVendor',
            'itemResults.repairImages'
        ]);
        
        return view('inspection.inspections.comprehensive', compact('vehicle', 'stages', 'vendors', 'existingInspection'));
    }

    /**
     * Update the specified vehicle inspection in storage.
     */
    public function update(Request $request, VehicleInspection $inspection)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*' => 'array',
            'items.*.status' => 'sometimes|in:pass,warning,fail,not_applicable',
            'items.*.notes' => 'nullable|string',
            'items.*.vendor_id' => 'nullable|exists:vendors,id',
            'items.*.cost' => 'nullable|numeric|min:0',
            'save_as_draft' => 'sometimes|boolean'
        ]);

        DB::beginTransaction();

        try {
            $needsRepair = false;
            $totalCost = 0;

            foreach ($validated['items'] as $itemId => $itemData) {
                $result = $inspection->itemResults()
                    ->where('inspection_item_id', $itemId)
                    ->first();

                if (!$result) {
                    // Create new result if it doesn't exist
                    $result = $inspection->itemResults()->create([
                        'inspection_item_id' => $itemId,
                        'status' => $itemData['status'] ?? 'not_applicable',
                        'notes' => $itemData['notes'] ?? null,
                        'cost' => $itemData['cost'] ?? 0,
                        'vendor_id' => $itemData['vendor_id'] ?? null,
                        'requires_repair' => isset($itemData['status']) && in_array($itemData['status'], ['warning', 'fail']),
                        'repair_completed' => false,
                    ]);
                } else {
                    // Update existing result
                    $result->update([
                        'status' => $itemData['status'] ?? $result->status,
                        'notes' => $itemData['notes'] ?? $result->notes,
                        'cost' => $itemData['cost'] ?? $result->cost,
                        'vendor_id' => $itemData['vendor_id'] ?? $result->vendor_id,
                        'requires_repair' => isset($itemData['status']) && in_array($itemData['status'], ['warning', 'fail']),
                    ]);
                }

                // Track if any items need repair
                if ($result->requires_repair) {
                    $needsRepair = true;
                }

                // Add to total cost if repair is needed
                if ($result->requires_repair) {
                    $totalCost += $result->cost;
                }
            }

            // Update inspection total cost
            $inspection->update([
                'total_cost' => $totalCost
            ]);

            // Update vehicle status if vendors are assigned
            $hasVendors = $inspection->itemResults()
                ->whereNotNull('vendor_id')
                ->exists();

            if ($hasVendors && $inspection->vehicle->status === 'needs_repair') {
                $inspection->vehicle->update(['status' => 'repair_assigned']);
            } elseif ($needsRepair) {
                $inspection->vehicle->update(['status' => 'needs_repair']);
            }
            
            DB::commit();
            
            return redirect()->route('inspection.inspections.show', $inspection)
                ->with('success', 'Inspection updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error updating inspection: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified vehicle inspection from storage.
     */
    public function destroy(VehicleInspection $inspection)
    {
        // Only allow deletion if the inspection is still pending and has no item results
        if ($inspection->status !== 'pending' || $inspection->itemResults()->count() > 0) {
            return redirect()->route('inspection.inspections.index')
                ->with('error', 'Cannot delete an inspection that is in progress or has results.');
        }

        $inspection->delete();

        return redirect()->route('inspection.inspections.index')
            ->with('success', 'Vehicle inspection deleted successfully.');
    }

    /**
     * Update inspection item results.
     */
    public function updateItems(Request $request, VehicleInspection $inspection)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:inspection_item_results,id',
            'items.*.status' => 'required|in:pass,warning,fail,not_applicable',
            'items.*.notes' => 'nullable|string',
            'items.*.requires_repair' => 'nullable|boolean',
            'items.*.cost' => 'nullable|numeric|min:0',
            'items.*.actual_cost' => 'nullable|numeric|min:0',
            'items.*.assigned_to_vendor_id' => 'nullable|exists:vendors,id',
            'items.*.repair_completed' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $totalEstimatedCost = 0;
            $totalActualCost = 0;

            foreach ($validated['items'] as $itemData) {
                $itemResult = InspectionItemResult::findOrFail($itemData['id']);
                
                // Ensure this item belongs to the current inspection
                if ($itemResult->vehicle_inspection_id !== $inspection->id) {
                    throw new \Exception("Item result doesn't belong to this inspection");
                }
                
                // Set requires_repair based on status - only for sales manager statuses
                // Repair = warning, Replace = fail
                $itemData['requires_repair'] = in_array($itemData['status'], ['warning', 'fail']);
                
                // Update the item result
                $itemResult->update($itemData);

                // Add to totals
                $totalEstimatedCost += $itemData['cost'] ?? 0;
                $totalActualCost += $itemData['actual_cost'] ?? 0;
            }
            
            // Update the inspection status if all items have been checked
            $this->updateInspectionStatus($inspection);
            
            // Update total costs
            $inspection->update([
                'total_cost' => $totalEstimatedCost
            ]);
            
            DB::commit();
            
            return redirect()->route('inspection.inspections.show', $inspection)
                ->with('success', 'Inspection items updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('inspection.inspections.show', $inspection)
                ->with('error', 'Error updating inspection items: ' . $e->getMessage());
        }
    }

    /**
     * Update the inspection status based on item results.
     */
    private function updateInspectionStatus(VehicleInspection $inspection)
    {
        $inspection->refresh();
        
        $totalItems = $inspection->itemResults->count();
        $checkedItems = $inspection->itemResults->filter(function ($result) {
            return $result->status !== 'not_applicable';
        })->count();
        
        // If all items have been checked
        if ($totalItems > 0 && $checkedItems === $totalItems) {
            if ($inspection->status === 'pending') {
                $inspection->update(['status' => 'in_progress']);
            }
            
            // If all repairs are completed, mark as completed
            $requiresRepair = $inspection->countItemsRequiringRepair();
            $completedRepairs = $inspection->countCompletedRepairs();
            
            if ($requiresRepair > 0 && $requiresRepair === $completedRepairs) {
                $inspection->update([
                    'status' => 'completed',
                    'completed_date' => now()
                ]);
            }
        }
    }

    /**
     * Upload images for an inspection item.
     */
    public function uploadImages(Request $request, InspectionItemResult $result)
    {
        $user = auth()->user();
        
        // Determine allowed image types based on user role
        $allowedImageTypes = [];
        if ($user->hasRole('Sales Manager')) {
            $allowedImageTypes = ['before'];
        } elseif ($user->vendor && $result->vendor_id === $user->vendor->id) {
            $allowedImageTypes = ['after', 'documentation'];
        } else {
           // abort(403, 'You do not have permission to upload images for this item.');
        }

        $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'image_type' => 'required|in:' . implode(',', $allowedImageTypes),
            'caption' => 'nullable|string|max:255',
        ]);

        foreach ($request->file('images') as $image) {
            // Create organized directory structure
            $path = sprintf(
                'inspections/%s/%s/%s',
                $result->vehicleInspection->vehicle->stock_number,
                $result->vehicleInspection->id,
                $result->id
            );
            
            // Store image with original name but sanitized
            $fileName = preg_replace('/[^a-zA-Z0-9.]/', '_', $image->getClientOriginalName());
            $fullPath = $image->storeAs($path, $fileName, 'public');
            
            // Create repair image record
            RepairImage::create([
                'inspection_item_result_id' => $result->id,
                'image_path' => $fullPath,
                'image_type' => $request->image_type,
                'caption' => $request->caption,
            ]);
        }

        return redirect()->back()->with('success', 'Images uploaded successfully.');
    }

    /**
     * Delete a repair image.
     */
    public function deleteImage(Request $request, $imageId)
    {
        $image = RepairImage::findOrFail($imageId);
        $image->delete();
        
        return redirect()->back()->with('success', 'Image deleted successfully.');
    }

    /**
     * Show the comprehensive inspection form.
     */
    public function comprehensive(Vehicle $vehicle)
    {
        // Check if vehicle already has an inspection
        $existingInspection = VehicleInspection::where('vehicle_id', $vehicle->id)
            ->latest()
            ->first();

        if ($existingInspection) {
            // Load existing inspection data
            $stages = InspectionStage::with(['inspectionItems' => function ($query) {
                $query->where('is_active', true)->orderBy('order');
            }])->where('is_active', true)->orderBy('order')->get();
            
            $vendors = Vendor::where('is_active', true)->orderBy('name')->get();

            // Pass the existing inspection data to the view
            return view('inspection.inspections.comprehensive', compact('vehicle', 'stages', 'vendors', 'existingInspection'));
        }
        
        // If no existing inspection, proceed with new inspection
        $stages = InspectionStage::with(['inspectionItems' => function ($query) {
            $query->where('is_active', true)->orderBy('order');
        }])->where('is_active', true)->orderBy('order')->get();
        
        $vendors = Vendor::where('is_active', true)->orderBy('name')->get();
        
        return view('inspection.inspections.comprehensive', compact('vehicle', 'stages', 'vendors'));
    }

    /**
     * Store or update a comprehensive inspection.
     */
    public function comprehensiveStore(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*' => 'array',
            'items.*.status' => 'sometimes|in:pass,warning,fail,not_applicable',
            'items.*.notes' => 'nullable|string',
            'items.*.vendor_id' => 'nullable|exists:vendors,id',
            'items.*.cost' => 'nullable|numeric|min:0',
            'save_as_draft' => 'sometimes|boolean',
        ]);

        // Check if it's a draft save
        $isDraft = $request->input('save_as_draft') == '1';

        // Check if vehicle already has an inspection
        $existingInspection = VehicleInspection::where('vehicle_id', $vehicle->id)
            ->latest()
            ->first();

        // Initialize needsRepair flag
        $needsRepair = false;
            
        DB::beginTransaction();
        
        try {
            if ($existingInspection) {
                // Update existing inspection
                foreach ($request->items as $itemId => $itemData) {
                    $item = InspectionItem::findOrFail($itemId);
                    $result = $existingInspection->itemResults()
                        ->where('inspection_item_id', $itemId)
                        ->first();

                    // Skip items without status in draft mode
                    if ($isDraft && !isset($itemData['status'])) {
                        continue;
                    }

                    if (!$result) {
                        // Create new result if it doesn't exist
                        $result = $existingInspection->itemResults()->create([
                            'inspection_item_id' => $itemId,
                            'status' => $itemData['status'] ?? 'not_applicable',
                            'notes' => $itemData['notes'] ?? null,
                            'cost' => $itemData['cost'] ?? 0,
                            'vendor_id' => $itemData['vendor_id'] ?? $request->vendor_id ?? null,
                            'requires_repair' => isset($itemData['status']) && in_array($itemData['status'], ['warning', 'fail']),
                            'repair_completed' => false,
                        ]);
                    } else {
                        // Update existing result
                        $result->update([
                            'status' => $itemData['status'] ?? $result->status,
                            'notes' => $itemData['notes'] ?? $result->notes,
                            'cost' => $itemData['cost'] ?? $result->cost,
                            'vendor_id' => $itemData['vendor_id'] ?? $request->vendor_id ?? $result->vendor_id,
                            'requires_repair' => isset($itemData['status']) && in_array($itemData['status'], ['warning', 'fail']),
                        ]);
                    }
                }

                // Update inspection status and total cost
                $totalCost = $existingInspection->itemResults->sum('cost');
                $existingInspection->update([
                    'total_cost' => $totalCost,
                    'status' => $isDraft ? 'in_progress' : 'completed',
                    'completed_date' => $isDraft ? null : now()
                ]);

                $needsRepair = $existingInspection->itemResults()
                    ->whereIn('status', ['warning', 'fail'])
                    ->exists();

            } else {
                // Create new inspection
                $inspection = VehicleInspection::create([
                    'vehicle_id' => $vehicle->id,
                    'status' => $isDraft ? 'in_progress' : 'completed',
                    'completed_date' => $isDraft ? null : now(),
                    'inspection_stage_id' => InspectionStage::first()->id,
                    'user_id' => auth()->id()
                ]);
                
                $totalCost = 0;
                $needsRepairItems = false;
                
                // Process each inspection item
                foreach ($request->items as $itemId => $itemData) {
                    // Skip items without status in draft mode
                    if ($isDraft && !isset($itemData['status'])) {
                        continue;
                    }

                    $item = InspectionItem::findOrFail($itemId);
                    $isRepairNeeded = isset($itemData['status']) && in_array($itemData['status'], ['warning', 'fail']);
                    
                    // If any item needs repair, set the flag
                    if ($isRepairNeeded) {
                        $needsRepairItems = true;
                    }
                    
                    // Create inspection result
                    $cost = $itemData['cost'] ?? 0;
                    $totalCost += ($isRepairNeeded ? $cost : 0);
                    
                    $result = $inspection->itemResults()->create([
                        'inspection_item_id' => $itemId,
                        'status' => $itemData['status'] ?? 'not_applicable',
                        'notes' => $itemData['notes'] ?? null,
                        'cost' => $cost,
                        'vendor_id' => $itemData['vendor_id'] ?? $request->vendor_id ?? null,
                        'requires_repair' => $isRepairNeeded,
                        'repair_completed' => false,
                    ]);
                }
                
                // Update the inspection with the total cost
                $inspection->update(['total_cost' => $totalCost]);
                
                // Set the flag based on the inspection results
                $needsRepair = $needsRepairItems;
            }
            
            // Only update vehicle status when not saving as draft
            if (!$isDraft) {
                if ($needsRepair && $request->vendor_id) {
                    $vehicle->update(['status' => 'repair_assigned']);
                } else if ($needsRepair) {
                    $vehicle->update(['status' => 'needs_repair']);
                } else {
                    $vehicle->update(['status' => 'ready']);
                }
            }
            
            DB::commit();
            
            if ($isDraft) {
                return redirect()->route('inspection.inspections.index')
                    ->with('success', 'Inspection saved as draft. You can continue working on it later.');
            }
            
            return redirect()->route('vehicles.show', $vehicle)
                ->with('success', 'Inspection ' . ($existingInspection ? 'updated' : 'completed') . ' successfully. ' . 
                ($needsRepair ? 'Repair items have been ' . ($request->vendor_id ? 'assigned to vendor.' : 'identified.') : 'Vehicle is ready.'));
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->with('error', 'Error ' . ($existingInspection ? 'updating' : 'saving') . ' inspection: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Update a comprehensive inspection for a vehicle.
     */
    public function comprehensiveUpdate(Request $request, Vehicle $vehicle)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*' => 'array',
            'items.*.status' => 'sometimes|in:pass,warning,fail,not_applicable',
            'items.*.notes' => 'nullable|string',
            'items.*.vendor_id' => 'nullable|exists:vendors,id',
            'items.*.cost' => 'nullable|numeric|min:0',
            'save_as_draft' => 'sometimes|boolean'
        ]);

        // Get the latest inspection for this vehicle
        $inspection = $vehicle->vehicleInspections()->latest()->firstOrFail();
        
        DB::beginTransaction();

        try {
            $needsRepair = false;
            $totalCost = 0;
            $hasAssignedVendors = false;

            foreach ($validated['items'] as $itemId => $itemData) {
                $result = $inspection->itemResults()
                    ->where('inspection_item_id', $itemId)
                    ->first();

                $isRepairNeeded = isset($itemData['status']) && in_array($itemData['status'], ['warning', 'fail']);
                $vendorId = isset($itemData['vendor_id']) && !empty($itemData['vendor_id']) ? $itemData['vendor_id'] : null;

                // If global vendor is set and item needs repair but has no specific vendor
                if ($isRepairNeeded && !$vendorId && $request->vendor_id) {
                    $vendorId = $request->vendor_id;
                }

                if (!$result) {
                    // Create new result if it doesn't exist
                    $result = $inspection->itemResults()->create([
                        'inspection_item_id' => $itemId,
                        'status' => $itemData['status'] ?? 'not_applicable',
                        'notes' => $itemData['notes'] ?? null,
                        'cost' => $itemData['cost'] ?? 0,
                        'vendor_id' => $vendorId,
                        'requires_repair' => $isRepairNeeded,
                        'repair_completed' => false,
                    ]);
                } else {
                    // Update existing result
                    $result->update([
                        'status' => $itemData['status'] ?? $result->status,
                        'notes' => $itemData['notes'] ?? $result->notes,
                        'cost' => $itemData['cost'] ?? $result->cost,
                        'vendor_id' => $vendorId,
                        'requires_repair' => $isRepairNeeded,
                    ]);
                }

                // Track if any items need repair
                if ($isRepairNeeded) {
                    $needsRepair = true;
                    if ($vendorId) {
                        $hasAssignedVendors = true;
                    }
                }

                // Add to total cost if repair is needed
                if ($isRepairNeeded) {
                    $totalCost += $itemData['cost'] ?? 0;
                }

                // Handle image uploads if present
                if ($request->hasFile("items.{$itemId}.images")) {
                    foreach ($request->file("items.{$itemId}.images") as $image) {
                        $path = $image->store('repair-images', 'public');
                        $result->repairImages()->create([
                            'path' => $path,
                            'original_name' => $image->getClientOriginalName()
                        ]);
                    }
                }
            }

            // Update inspection status and total cost
            $inspection->update([
                'total_cost' => $totalCost,
                'status' => $request->input('save_as_draft') ? 'in_progress' : 'completed',
                'completed_at' => $request->input('save_as_draft') ? null : now()
            ]);

            // Update vehicle status when not saving as draft
            if (!$request->input('save_as_draft')) {
                if ($needsRepair && $hasAssignedVendors) {
                    $vehicle->update(['status' => 'repair_assigned']);
                } else if ($needsRepair) {
                    $vehicle->update(['status' => 'needs_repair']);
                } else {
                    $vehicle->update(['status' => 'ready']);
                }
            }

            DB::commit();

            return redirect()
                ->route('vehicles.show', $vehicle)
                ->with('success', 'Inspection updated successfully. ' . 
                    ($needsRepair ? 'Repair items have been ' . ($hasAssignedVendors ? 'assigned to vendors.' : 'identified.') : 'Vehicle is ready.'));

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Failed to update inspection. ' . $e->getMessage());
        }
    }

    /**
     * Start a new inspection for a vehicle.
     */
    public function startInspection(Vehicle $vehicle)
    {
        // Redirect to comprehensive inspection instead of step-by-step
        return redirect()->route('comprehensive.show', $vehicle)
            ->with('info', 'Using the comprehensive inspection workflow for better efficiency.');
    }

    /**
     * Mark an inspection as completed.
     */
    public function markComplete(Request $request, VehicleInspection $inspection)
    {
        // Check if all inspection items have been assessed
        $totalItems = $inspection->inspectionStage->inspectionItems->count();
        $assessedItems = $inspection->itemResults->whereIn('status', ['pass', 'warning', 'fail'])->count();
        
        if ($assessedItems < $totalItems) {
            return redirect()->back()->with('error', 'All inspection items must be assessed before completing the inspection.');
        }
        
        $inspection->update([
            'status' => 'completed',
            'completed_date' => now()
        ]);
        
        // Check if all inspections for this vehicle are completed
        $vehicle = $inspection->vehicle;
        $pendingInspections = VehicleInspection::where('vehicle_id', $vehicle->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->count();
        
        if ($pendingInspections === 0) {
            $vehicle->update(['status' => Vehicle::STATUS_READY_FOR_SALE]);
        }
        
        return redirect()->route('inspection.inspections.index')
            ->with('success', 'Inspection marked as completed successfully.');
    }

    /**
     * Add a method after markComplete to assign to sales team
     * We'll place this after markComplete and before handleInspectionItem
     */
    public function assignToSales(VehicleInspection $inspection)
    {
        try {
            // Check if inspection is completed
            if ($inspection->status !== 'completed') {
                return redirect()->back()->with('error', 'Vehicle inspection must be completed before assigning to sales team.');
            }
            
            // Check if all repairs are completed
            $needsRepairItems = $inspection->itemResults()
                ->where('requires_repair', true)
                ->where('repair_completed', false)
                ->count();
                
            if ($needsRepairItems > 0) {
                return redirect()->back()->with('error', 'All repairs must be completed before assigning to sales team.');
            }
            
            // Get the vehicle from the inspection
            $vehicle = $inspection->vehicle;
            
            // Verify we have a valid vehicle
            if (!$vehicle || !$vehicle->id) {
                \Log::error('Invalid vehicle in assignToSales method', [
                    'inspection_id' => $inspection->id,
                    'vehicle' => $vehicle
                ]);
                return redirect()->back()->with('error', 'Could not find the vehicle associated with this inspection.');
            }
            
            // Only update status if it's not already repairs_completed
            if ($vehicle->status !== Vehicle::STATUS_REPAIRS_COMPLETED) {
                $vehicle->update([
                    'status' => Vehicle::STATUS_READY_FOR_SALE
                ]);
            }
            
            \Log::info('Assigning vehicle to sales', [
                'vehicle_id' => $vehicle->id,
                'vehicle_status' => $vehicle->status
            ]);
            
            // Redirect to sales assignment create page with the vehicle ID directly in the URL
            return redirect()->route('sales-assignments.create', $vehicle->id)
                ->with('success', 'Vehicle is ready for sales team assignment.');
        } catch (\Exception $e) {
            \Log::error('Error in assignToSales method', [
                'inspection_id' => $inspection->id ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    protected function handleInspectionItem($inspection, $itemId, $itemData)
    {
        $result = $inspection->itemResults()->updateOrCreate(
            ['inspection_item_id' => $itemId],
            [
                'status' => $itemData['status'] ?? null,
                'notes' => $itemData['notes'] ?? null,
                'vendor_id' => $itemData['vendor_id'] ?? null,
                'cost' => $itemData['cost'] ?? null,
                'completion_notes' => $itemData['completion_notes'] ?? null,
                'repair_completed' => isset($itemData['repair_completed']) ? true : false,
            ]
        );

        // Handle repair images if they exist
        if (isset($itemData['repair_images']) && is_array($itemData['repair_images'])) {
            foreach ($itemData['repair_images'] as $image) {
                if ($image->isValid()) {
                    $stockNumber = $inspection->vehicle->stock_number;
                    $path = $image->store("public/inspections/{$stockNumber}/{$inspection->id}/repairs");
                    
                    // Create repair image record
                    $result->repairImages()->create([
                        'image_path' => str_replace('public/', '', $path),
                        'type' => $itemData['status'] === 'warning' ? 'repair' : 'replace',
                        'original_name' => $image->getClientOriginalName()
                    ]);
                }
            }
        }

        return $result;
    }

    protected function handleRepairCompletion($inspection, $itemId, $itemData)
    {
        $result = $inspection->itemResults()->where('inspection_item_id', $itemId)->first();
        
        if ($result) {
            $result->update([
                'repair_completed' => true,
                'completion_notes' => $itemData['completion_notes'] ?? null,
                'completed_at' => now(),
            ]);

            // Handle repair completion images
            if (isset($itemData['repair_images']) && is_array($itemData['repair_images'])) {
                foreach ($itemData['repair_images'] as $image) {
                    if ($image->isValid()) {
                        $stockNumber = $inspection->vehicle->stock_number;
                        $path = $image->store("public/inspections/{$stockNumber}/{$inspection->id}/repairs/completed");
                        
                        // Create repair image record with completion flag
                        $result->repairImages()->create([
                            'image_path' => str_replace('public/', '', $path),
                            'type' => $result->status === 'warning' ? 'repair_completed' : 'replace_completed',
                            'original_name' => $image->getClientOriginalName()
                        ]);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Mark a vehicle as ready for sale after inspection
     *
     * @param Request $request
     * @param VehicleInspection $inspection
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsReadyForSale(VehicleInspection $inspection)
    {
        // Check if user has permission
        if (!Auth::user()->hasRole(['Admin', 'Sales Manager'])) {
            return redirect()->back()->with('error', 'You do not have permission to perform this action.');
        }

        $vehicle = $inspection->vehicle;

        // Update the vehicle status
        $success = $vehicle->markAsReadyForSale();

        if ($success) {
            // Log the status change
            \Log::info("Vehicle marked as ready for sale", [
                'vehicle_id' => $vehicle->id,
                'inspection_id' => $inspection->id,
                'from_status' => $vehicle->getOriginal('status'),
                'to_status' => Vehicle::STATUS_READY_FOR_SALE,
                'user_id' => Auth::id(),
            ]);

            return redirect()->back()->with('success', "Vehicle marked as Ready for Sale successfully.");
        }

        return redirect()->back()->with('error', 'Failed to update vehicle status.');
    }
} 