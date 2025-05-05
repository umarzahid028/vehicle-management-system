<?php

namespace App\Http\Controllers\Recon;

use App\Http\Controllers\Controller;
use App\Models\InspectionItemResult;
use App\Models\RepairImage;
use App\Models\Vendor;
use App\Models\Vehicle;
use App\Models\VehicleInspection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class OffsiteInspectionController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Recon Manager');
    }

    /**
     * Display a listing of the offsite vendor inspections.
     */
    public function index(): View
    {
        // Get all items that are assigned to offsite vendors and not completed
        $offsiteInspectionItems = InspectionItemResult::with([
                'vehicleInspection.vehicle',
                'inspectionItem',
                'assignedVendor.type',
                'repairImages'
            ])
            ->whereHas('assignedVendor', function($query) {
                $query->whereHas('type', function($q) {
                    $q->where('is_on_site', false);
                });
            })
            
            ->latest()
            ->paginate(15);

        // Group by vehicle for better display organization
        $itemsByVehicle = $offsiteInspectionItems->groupBy('vehicleInspection.vehicle_id');
       
        return view('recon.offsite-inspections.index', compact('offsiteInspectionItems', 'itemsByVehicle'));
    }

    /**
     * Display the items for a specific vehicle.
     */
    public function show(Vehicle $vehicle): View
    {
        // Get all items for this vehicle that are assigned to offsite vendors
        $offsiteItems = InspectionItemResult::with([
                'vehicleInspection',
                'inspectionItem',
                'assignedVendor.type',
                'repairImages'
            ])
            ->whereHas('vehicleInspection', function($query) use ($vehicle) {
                $query->where('vehicle_id', $vehicle->id);
            })
            ->whereHas('assignedVendor', function($query) {
                $query->whereHas('type', function($q) {
                    $q->where('is_on_site', false);
                });
            })
            ->where('requires_repair', true)
            ->get();
            
        return view('recon.offsite-inspections.show', compact('vehicle', 'offsiteItems'));
    }

    /**
     * Update the status of an inspection item.
     */
    public function updateItemStatus(Request $request, InspectionItemResult $item)
    {
        $validated = $request->validate([
            'status' => 'required|in:in_progress,completed,cancelled',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
        ]);
        
        // Start a transaction to ensure data consistency
        DB::beginTransaction();
        
        try {
            // Update the status, notes and cost
            $item->update([
                'status' => $validated['status'],
                'notes' => $validated['notes'] ?? $item->notes,
                'cost' => $validated['cost'] ?? $item->cost,
                'completed_at' => $validated['status'] === 'completed' ? now() : $item->completed_at,
                'repair_completed' => $validated['status'] === 'completed' ? true : false,
            ]);
            
            // If the item is marked as completed, check if all items for this inspection are completed
            if ($validated['status'] === 'completed') {
                $inspection = $item->vehicleInspection;
                $allCompleted = $inspection->itemResults()
                    ->where('requires_repair', true)
                    ->where('repair_completed', false)
                    ->doesntExist();
                
                if ($allCompleted) {
                    // All items are repaired, update the inspection and vehicle status
                    $inspection->update([
                        'status' => 'completed',
                        'completed_date' => now(),
                    ]);
                    
                    $inspection->vehicle->update([
                        'status' => \App\Models\Vehicle::STATUS_REPAIRS_COMPLETED
                    ]);
                }
            }
            
            DB::commit();
            
            $statusMessages = [
                'in_progress' => 'Work started on this item.',
                'completed' => 'Inspection item marked as completed.',
                'cancelled' => 'Inspection item cancelled.'
            ];
            
            return redirect()->back()->with('success', $statusMessages[$validated['status']]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update item status: ' . $e->getMessage());
        }
    }

    /**
     * Upload repair images for an inspection item.
     */
    public function uploadImages(Request $request, InspectionItemResult $item)
    {
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $uploadedImages = [];
        
        foreach ($request->file('images') as $image) {
            $path = $image->store('repair-images', 'public');
            
            $repairImage = RepairImage::create([
                'inspection_item_result_id' => $item->id,
                'image_path' => $path,
                'caption' => 'Repair image uploaded by Recon Manager',
                'uploaded_by' => auth()->id(),
            ]);
            
            $uploadedImages[] = $repairImage;
        }
        
        return redirect()->back()->with('success', count($uploadedImages) . ' images uploaded successfully.');
    }

    /**
     * Delete a repair image.
     */
    public function deleteImage(RepairImage $repairImage)
    {
        if ($repairImage->uploaded_by !== auth()->id() && !auth()->user()->hasRole('Admin')) {
            return redirect()->back()->with('error', 'You do not have permission to delete this image.');
        }
        
        try {
            Storage::disk('public')->delete($repairImage->image_path);
            $repairImage->delete();
            
            return redirect()->back()->with('success', 'Image deleted successfully.');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete image: ' . $e->getMessage());
        }
    }
} 