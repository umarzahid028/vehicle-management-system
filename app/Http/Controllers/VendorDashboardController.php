<?php

namespace App\Http\Controllers;

use App\Models\VehicleInspection;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorDashboardController extends Controller
{
    public function index(): View
    {
     
        $user = auth()->user();
        $vendor = $user->vendor;
        
        if (!$vendor) {
            abort(403, 'No vendor profile found for this user.');
        }

        // Get all assigned inspections with related data
        $assignedInspections = VehicleInspection::with(['vehicle', 'itemResults'])
            ->whereHas('itemResults', function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Pending inspections (items that need attention)
        $pendingInspections = $assignedInspections->filter(function ($inspection) use ($vendor) {
            return $inspection->itemResults
                ->where('vendor_id', $vendor->id)
                ->where('status', '!=', 'pass')
                ->contains(function ($item) {
                    return in_array($item->status, ['pending', 'fail', 'warning'])
                        || !empty($item->diagnostic_status)
                        || ($item->requires_repair && !$item->repair_completed);
                });
        });

        // Completed inspections
        $completedInspections = $assignedInspections->filter(function ($inspection) use ($vendor) {
            $vendorItems = $inspection->itemResults->where('vendor_id', $vendor->id);
            
            return $vendorItems->isNotEmpty() && $vendorItems->every(function ($item) {
                return $item->repair_completed || $item->status === 'cancelled';
            });
        });

        // Calculate dashboard statistics
        $stats = [
            'total_assigned' => $assignedInspections->count(),
            'pending_count' => $pendingInspections->count(),
            'completed_count' => $completedInspections->count(),
            'urgent_count' => $pendingInspections->filter(function ($inspection) {
                return $inspection->created_at->diffInDays() > 2;
            })->count(),
            'this_week_completed' => $completedInspections->filter(function ($inspection) {
                return $inspection->completed_date?->isCurrentWeek();
            })->count(),
            'total_items' => $assignedInspections->sum(function ($inspection) use ($vendor) {
                return $inspection->itemResults->where('vendor_id', $vendor->id)->count();
            }),
            'pending_approval' => $assignedInspections->filter(function ($inspection) use ($vendor) {
                return $inspection->itemResults
                    ->where('vendor_id', $vendor->id)
                    ->contains(function ($item) {
                        return !empty($item->diagnostic_status) && $item->diagnostic_status === 'pending_approval';
                    });
            })->count(),
        ];

        // Get recent activity
        $recentActivity = $assignedInspections
            ->flatMap(function ($inspection) use ($vendor) {
                return $inspection->itemResults
                    ->where('vendor_id', $vendor->id)
                    ->map(function ($item) use ($inspection) {
                        return [
                            'type' => 'inspection_update',
                            'inspection' => $inspection,
                            'item' => $item,
                            'date' => $item->updated_at,
                            'status' => $item->status,
                        ];
                    });
            })
            ->sortByDesc('date')
            ->take(10);

        // Return the dashboard view with data
        return view('dashboards.vendor', compact(
            'pendingInspections',
            'completedInspections',
            'stats',
            'recentActivity'
        ));
    }

    public function inspectionHistory(): View
    {
        
        $user = auth()->user();
        $vendor = $user->vendor;
        
        if (!$vendor) {
            abort(403, 'No vendor profile found for this user.');
        }

        $completedInspections = VehicleInspection::with([
            'vehicle',
            'itemResults' => function ($query) use ($vendor) {
                $query->where('vendor_id', $vendor->id);
            },
            'itemResults.inspectionItem' // Load the inspection item details
        ])
        ->whereHas('itemResults', function ($query) use ($vendor) {
            $query->where('vendor_id', $vendor->id)
                ->where(function($q) {
                    $q->where('repair_completed', true)
                        ->orWhere('status', 'completed')
                        ->orWhere('status', 'cancelled');
                });
        })
        ->orderBy('completed_date', 'desc')
        ->get();

        return view('vendor.inspection-history', compact('completedInspections'));
    }

    public function showInspection(VehicleInspection $inspection): View
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
  
        $inspection->load([
            'vehicle', 
            'itemResults' => function ($query) use ($vendor) {
                // Only load item results assigned to this vendor
                $query->where('vendor_id', $vendor->id)
                      // Only include items that need attention (not passing or requiring repair)
                      ->where(function($q) {
                          $q->where('status', '!=', 'pass')
                            ->orWhere('requires_repair', true);
                      });
            },
            'itemResults.inspectionItem',
            'itemResults.repairImages'
        ]);
      
        return view('vendor.inspection-details', compact('inspection'));
    }

    public function submitEstimate(Request $request, VehicleInspection $inspection)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        if (!$vendor) {
            abort(403, 'No vendor profile found for this user.');
        }

        // Validate the request
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:inspection_item_results,id',
            'items.*.estimated_cost' => 'required|numeric|min:0',
            'items.*.notes' => 'nullable|string',
        ]);

        // Ensure vendor has access to these items
        foreach ($validated['items'] as $item) {
            $inspectionItem = $inspection->itemResults()->find($item['id']);
            
            if (!$inspectionItem || $inspectionItem->vendor_id !== $vendor->id) {
                abort(403);
            }

            $inspectionItem->update([
                'estimated_cost' => $item['estimated_cost'],
                'notes' => $item['notes'],
                'status' => 'pending_approval',
                'estimate_submitted_at' => now(),
            ]);
        }

        return redirect()->back()->with('success', 'Estimate submitted successfully.');
    }

    public function updateServiceStatus(Request $request, VehicleInspection $inspection)
    {
        $user = auth()->user();
        $vendor = $user->vendor;
        
        if (!$vendor) {
            abort(403, 'No vendor profile found for this user.');
        }

        // Validate the request
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:inspection_item_results,id',
            'items.*.status' => 'required|in:completed,cancelled',
            'items.*.actual_cost' => 'required_if:items.*.status,completed|nullable|numeric|min:0',
            'items.*.completion_notes' => 'nullable|string',
            'items.*.photos.*' => 'nullable|image|max:5120', // 5MB max
        ]);

        // Process each item
        foreach ($validated['items'] as $item) {
            $inspectionItem = $inspection->itemResults()->find($item['id']);
            
            if (!$inspectionItem || $inspectionItem->vendor_id !== $vendor->id) {
                abort(403);
            }

            // Handle photo uploads if any
            $photos = [];
            if ($request->hasFile("items.{$item['id']}.photos")) {
                foreach ($request->file("items.{$item['id']}.photos") as $photo) {
                    $path = $photo->store('inspection-photos', 'public');
                    $photos[] = $path;
                }
            }

            $inspectionItem->update([
                'status' => $item['status'],
                'actual_cost' => $item['actual_cost'],
                'completion_notes' => $item['completion_notes'],
                'photos' => $photos,
                'completed_at' => $item['status'] === 'completed' ? now() : null,
            ]);
        }

        // Check if all items are completed or cancelled
        $allCompleted = $inspection->itemResults()
            ->where('vendor_id', $vendor->id)
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->doesntExist();

        if ($allCompleted) {
            $inspection->update(['status' => 'completed']);
        }

        return redirect()->back()->with('success', 'Service status updated successfully.');
    }
} 