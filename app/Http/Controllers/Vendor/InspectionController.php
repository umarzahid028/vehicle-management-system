<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VehicleInspection;
use App\Models\InspectionItemResult;
use App\Models\RepairImage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use App\Models\InspectionItem;

class InspectionController extends Controller
{
    /**
     * Display a listing of the assigned inspections.
     */
    public function index(): View
    {
        
        $user = auth()->user();
        $vendor = $user->vendor;
        
        if (!$vendor) {
            abort(403, 'No vendor profile found for this user.');
        }
        
        $assignedInspections = VehicleInspection::with(['vehicle', 'inspectionItems'])
            ->whereHas('inspectionItems', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
                
            })

            ->orderBy('created_at', 'desc')
            ->get();
        //    dd($assignedInspections);
        return view('vendor.inspections.index', compact('assignedInspections'));
    }

    /**
     * Display the specified inspection.
     */
    public function show(VehicleInspection $inspection): View
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        if (!$vendor) {
            abort(403, 'No vendor profile found for this user.');
        }

        // Ensure the vendor has access to this inspection
        if (!$inspection->itemResults()->where('vendor_id', $vendor->id)->exists()) {
            abort(403);
        }

        // Load all inspection items assigned to this vendor, not just repair/replace items
        $inspection->load([
            'vehicle',
            'itemResults' => function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            },
            'itemResults.inspectionItem',
            'itemResults.repairImages'
        ]);

        return view('vendor.inspections.show', compact('inspection'));
    }

    /**
     * Update the inspection item status.
     * 
     * Sales Manager statuses: pass, warning (repair), fail (replace)
     * Vendor statuses: in_progress, completed, cancelled
     */
    public function updateItemStatus(Request $request, InspectionItemResult $item)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        if (!$vendor) {
            abort(403, 'No vendor profile found for this user.');
        }

        // Ensure the vendor owns this item
        if ($item->vendor_id !== $vendor->id) {
            abort(403);
        }

        // Ensure the item is in a status that can be acted upon by vendors
        // Only items marked for repair (warning) or replacement (fail) can be updated
        if (!in_array($item->status, ['warning', 'fail', 'in_progress'])) {
            return redirect()->back()->with('error', 'This item cannot be updated. It must be marked for repair or replacement first.');
        }

        $validated = $request->validate([
            'status' => 'required|in:in_progress,completed,cancelled',
            'actual_cost' => 'required_if:status,completed|nullable|numeric|min:0',
            'completion_notes' => 'nullable|string',
        ]);

        // Different handling based on status
        if ($validated['status'] === 'in_progress') {
            $updateData = [
                'status' => 'in_progress',
                'started_at' => now(),
            ];
        } else {
            // For completed status, we need to determine if this was originally 
            // a "repair" (warning) or "replace" (fail) item
            if ($validated['status'] === 'completed') {
                // If status was originally 'warning' (repair) or 'fail' (replace)
                // Keep track that repair was completed
                $originalStatus = $item->status === 'warning' ? 'repair' : 'replace';
                
                $updateData = [
                    'status' => 'completed',
                    'actual_cost' => $validated['actual_cost'],
                    'completion_notes' => $validated['completion_notes'] ?? null,
                    'completed_at' => now(),
                    'repair_completed' => true,
                    'requires_repair' => true, // Ensure requires_repair is set to true if completed
                ];
            } else {
                // For cancelled status
                $updateData = [
                    'status' => 'cancelled',
                    'completion_notes' => $validated['completion_notes'] ?? null,
                    'completed_at' => now(),
                    'repair_completed' => false,
                ];
            }
        }

        $item->update($updateData);

        // Only check for all completed if marking items as completed/cancelled
        if ($validated['status'] !== 'in_progress') {
            // Check if all items are completed
            $inspection = $item->vehicleInspection;
            $allCompleted = $inspection->itemResults()
                ->where('vendor_id', $vendor->id)
                ->whereNull('completed_at')
                ->doesntExist();

            if ($allCompleted) {
                $inspection->update([
                    'status' => 'completed',
                    'completed_date' => now(),
                ]);
                
                // Fix any inconsistent data - set all completed items to have repair_completed = true
                $inspection->itemResults()
                    ->where('vendor_id', $vendor->id)
                    ->where('status', 'completed')
                    ->update(['repair_completed' => true]);
                
                // Check if all repairs are completed for the vehicle
                $vehicle = $inspection->vehicle;
                $needsRepairItems = $inspection->itemResults()
                    ->where('requires_repair', true)
                    ->where('repair_completed', false)
                    ->count();
                
                if ($needsRepairItems === 0) {
                    // All repairs are completed, update vehicle status
                    $vehicle->update(['status' => \App\Models\Vehicle::STATUS_REPAIRS_COMPLETED]);
                }
            }
        }

        $statusMessages = [
            'in_progress' => 'Work started on this item.',
            'completed' => 'Inspection item marked as completed.',
            'cancelled' => 'Inspection item cancelled.'
        ];

        return redirect()->back()->with('success', $statusMessages[$validated['status']]);
    }

    /**
     * Upload images for an inspection item
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $item
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadImages(Request $request, $item)
    {
        $request->validate([
            'image_type' => 'required|in:before,after,documentation',
            'images.*' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            'caption' => 'nullable|string|max:255',
        ]);

        if (!$request->hasFile('images')) {
            return redirect()->back()->with('error', 'No images were uploaded.');
        }

        $vendor = auth()->user()->vendor;

        // Find the inspection item result for this vendor
        $itemResult = InspectionItemResult::where('id', $item)
            ->where('vendor_id', $vendor->id)
            ->firstOrFail();

        // The above query already ensures this vendor has access to this item
        // so we don't need the additional permission check that was here

        // Ensure item is in progress or already started
        if (!in_array($itemResult->status, ['in_progress', 'diagnostic'])) {
            return redirect()->back()->with('error', 'You can only upload images for items that are in progress.');
        }

        foreach ($request->file('images') as $image) {
            // Create organized directory structure
            $path = sprintf(
                'inspections/%s/%s/%s',
                $itemResult->vehicleInspection->vehicle->stock_number,
                $itemResult->vehicleInspection->id,
                $itemResult->id
            );
            
            // Store image with original name but sanitized
            $fileName = preg_replace('/[^a-zA-Z0-9.]/', '_', $image->getClientOriginalName());
            $fullPath = $image->storeAs($path, $fileName, 'public');
            
            RepairImage::create([
                'inspection_item_result_id' => $itemResult->id,
                'image_path' => $fullPath,
                'image_type' => $request->image_type,
                'caption' => $request->caption,
            ]);
        }

        return redirect()->back()->with('success', 'Images uploaded successfully.');
    }
} 