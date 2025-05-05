<?php

namespace App\Http\Controllers;

use App\Models\InspectionItemResult;
use App\Models\RepairImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InspectionItemResultController extends Controller
{
    /**
     * Store a newly created inspection item result.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_inspection_id' => 'required|exists:vehicle_inspections,id',
            'inspection_item_id' => 'required|exists:inspection_items,id',
            'status' => 'required|in:pass,warning,fail,not_applicable',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'vendor_id' => 'nullable|exists:vendors,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Create the result
        $result = InspectionItemResult::create($validated);
        
        // Update the inspection status
        $inspection = $result->vehicleInspection;
        if ($inspection->status === 'pending') {
            $inspection->update(['status' => 'in_progress']);
        }
        
        // Update total cost
        $totalCost = $inspection->itemResults->sum('cost');
        $inspection->update(['total_cost' => $totalCost]);
        
        // Handle images if any
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('repair-images', 'public');
                
                RepairImage::create([
                    'inspection_item_result_id' => $result->id,
                    'image_path' => $path,
                    'image_type' => $request->image_type ?? 'documentation',
                    'caption' => $request->caption ?? null,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Assessment saved successfully.');
    }

    /**
     * Update the specified inspection item result.
     */
    public function update(Request $request, InspectionItemResult $result)
    {
        $validated = $request->validate([
            'status' => 'required|in:pass,warning,fail,not_applicable',
            'notes' => 'nullable|string',
            'cost' => 'nullable|numeric|min:0',
            'vendor_id' => 'nullable|exists:vendors,id',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Update the result
        $result->update($validated);
        
        // Update total cost for the inspection
        $inspection = $result->vehicleInspection;
        $totalCost = $inspection->itemResults->sum('cost');
        $inspection->update(['total_cost' => $totalCost]);
        
        // Handle images if any
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('repair-images', 'public');
                
                RepairImage::create([
                    'inspection_item_result_id' => $result->id,
                    'image_path' => $path,
                    'image_type' => $request->image_type ?? 'documentation',
                    'caption' => $request->caption ?? null,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Assessment updated successfully.');
    }

    /**
     * Remove the specified inspection item result.
     */
    public function destroy(InspectionItemResult $result)
    {
        // Only allow deletion if no attachments and if inspection is not completed
        if ($result->vehicleInspection->status === 'completed') {
            return redirect()->back()->with('error', 'Cannot delete a result from a completed inspection.');
        }
        
        // Delete associated images
        foreach ($result->repairImages as $image) {
            if (Storage::disk('public')->exists($image->image_path)) {
                Storage::disk('public')->delete($image->image_path);
            }
            $image->delete();
        }
        
        $result->delete();
        
        return redirect()->back()->with('success', 'Assessment deleted successfully.');
    }

    /**
     * Assign a vendor to an inspection item result.
     */
    public function assignVendor(Request $request, InspectionItemResult $result)
    {
        $validated = $request->validate([
            'vendor_id' => 'required|exists:vendors,id',
            'is_vendor_visible' => 'boolean',
            'diagnostic_status' => 'nullable|string',
        ]);
        
        $result->update([
            'vendor_id' => $validated['vendor_id'],
            'is_vendor_visible' => $validated['is_vendor_visible'] ?? false,
            'diagnostic_status' => $validated['diagnostic_status'],
            'assigned_at' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Vendor assigned successfully');
    }

    /**
     * Mark an inspection item result as complete.
     */
    public function markComplete(Request $request, InspectionItemResult $result)
    {
        $validated = $request->validate([
            'repair_completed' => 'boolean',
            'notes' => 'nullable|string',
        ]);
        
        $result->update([
            'repair_completed' => $validated['repair_completed'] ?? true,
            'notes' => $validated['notes'] ?? $result->notes,
            'completed_at' => now(),
        ]);
        
        return redirect()->back()->with('success', 'Repair marked as completed');
    }

    /**
     * Upload a photo for an inspection item result.
     */
    public function uploadPhoto(Request $request, InspectionItemResult $result)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // Delete old photo if exists
        if ($result->photo_path && Storage::disk('public')->exists($result->photo_path)) {
            Storage::disk('public')->delete($result->photo_path);
        }

        // Store the new photo
        $path = $request->file('photo')->store('inspection-photos', 'public');
        
        // Update the result with the new photo path
        $result->update([
            'photo_path' => $path,
        ]);

        return redirect()->back()->with('success', 'Photo uploaded successfully.');
    }
} 