<?php

namespace App\Http\Controllers;

use App\Models\VendorEstimate;
use App\Models\InspectionItemResult;
use App\Models\User;
use App\Notifications\NewEstimateRequiresApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorEstimateController extends Controller
{
    /**
     * Store a newly created vendor estimate in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inspection_item_result_id' => 'required|exists:inspection_item_results,id',
            'vendor_id' => 'required|exists:vendors,id',
            'estimated_cost' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);
        
        $estimate = VendorEstimate::create([
            'inspection_item_result_id' => $validated['inspection_item_result_id'],
            'vendor_id' => $validated['vendor_id'],
            'estimated_cost' => $validated['estimated_cost'],
            'description' => $validated['description'],
            'status' => 'pending',
        ]);
        
        // Notify users who can approve estimates
        $approvers = User::whereIn('role', ['admin', 'manager', 'sales_manager', 'recon_manager'])->get();
        foreach ($approvers as $approver) {
            $approver->notify(new NewEstimateRequiresApproval($estimate));
        }
        
        return redirect()->back()->with('success', 'Estimate submitted for approval');
    }
    
    /**
     * Approve a vendor estimate.
     */
    public function approve(VendorEstimate $estimate)
    {
        $estimate->update([
            'status' => 'approved',
            'approved_by_user_id' => Auth::id(),
            'approved_at' => now(),
        ]);
        
        // Update the inspection item result with the approved cost
        $itemResult = $estimate->inspectionItemResult;
        $itemResult->update([
            'cost' => $estimate->estimated_cost,
        ]);
        
        // Update the inspection's total cost
        $inspection = $itemResult->vehicleInspection;
        $inspection->update([
            'total_cost' => $inspection->calculateTotalCost()
        ]);
        
        return redirect()->back()->with('success', 'Estimate approved successfully');
    }
    
    /**
     * Reject a vendor estimate.
     */
    public function reject(Request $request, VendorEstimate $estimate)
    {
        $validated = $request->validate([
            'rejected_reason' => 'required|string',
        ]);
        
        $estimate->update([
            'status' => 'rejected',
            'approved_by_user_id' => Auth::id(),
            'rejected_reason' => $validated['rejected_reason'],
        ]);
        
        return redirect()->back()->with('success', 'Estimate rejected');
    }
    
    /**
     * Display a listing of pending estimates.
     */
    public function pendingEstimates()
    {
        $pendingEstimates = VendorEstimate::where('status', 'pending')
            ->with([
                'inspectionItemResult.vehicleInspection.vehicle', 
                'inspectionItemResult.inspectionItem',
                'vendor'
            ])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('vendor-estimates.pending', compact('pendingEstimates'));
    }
}
