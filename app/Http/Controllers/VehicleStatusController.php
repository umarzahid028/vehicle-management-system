<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Services\VehicleStatusService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class VehicleStatusController extends Controller
{
    protected $vehicleStatusService;

    public function __construct(VehicleStatusService $vehicleStatusService)
    {
        $this->vehicleStatusService = $vehicleStatusService;
        $this->middleware('auth');
    }

    /**
     * Display a list of available statuses and transitions for a vehicle
     *
     * @param Vehicle $vehicle
     * @return \Illuminate\Http\Response
     */
    public function getAvailableStatuses(Vehicle $vehicle)
    {
        $currentStatus = $vehicle->status;
        $availableTransitions = $vehicle->getAvailableStatusTransitions();
        $allStatuses = $this->vehicleStatusService->getAllStatuses();
        $category = $vehicle->getStatusCategory();

        return response()->json([
            'current_status' => $currentStatus,
            'available_transitions' => $availableTransitions,
            'all_statuses' => $allStatuses,
            'category' => $category,
        ]);
    }

    /**
     * Update the status of a vehicle
     *
     * @param Request $request
     * @param Vehicle $vehicle
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'status' => 'required|string',
            'notes' => 'nullable|string',
        ]);

        $newStatus = $request->input('status');
        $notes = $request->input('notes');
        $userId = Auth::id();

        // Check if the transition is valid
        if (!$this->vehicleStatusService->isValidTransition($vehicle->status, $newStatus)) {
            return response()->json([
                'success' => false,
                'message' => "Cannot transition from {$vehicle->status} to {$newStatus}",
            ], 422);
        }

        // Additional data to update
        $additionalData = [
            'updated_by' => $userId,
        ];

        // Specific handling for certain statuses
        switch ($newStatus) {
            case Vehicle::STATUS_SOLD:
                $additionalData['sold_date'] = now();
                break;
            
            case Vehicle::STATUS_READY_FOR_SALE_ASSIGNED:
                $request->validate([
                    'sales_team_id' => 'required|exists:users,id',
                ]);
                $additionalData['sales_team_id'] = $request->input('sales_team_id');
                $additionalData['assigned_for_sale_by'] = $userId;
                $additionalData['assigned_for_sale_at'] = now();
                break;
        }

        // Update the status
        $success = $this->vehicleStatusService->updateStatus($vehicle, $newStatus, $additionalData);

        if ($success) {
            // Log the status change
            Log::info("Vehicle status updated", [
                'vehicle_id' => $vehicle->id,
                'from_status' => $vehicle->getOriginal('status'),
                'to_status' => $newStatus,
                'user_id' => $userId,
                'notes' => $notes,
            ]);

            // Could add code here to notify relevant users about the status change

            return response()->json([
                'success' => true,
                'message' => "Vehicle status updated to {$newStatus}",
                'vehicle' => $vehicle->fresh(),
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to update vehicle status',
        ], 500);
    }

    /**
     * Get vehicles filtered by status
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getVehiclesByStatus(Request $request)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $status = $request->input('status');
        
        // Get the vehicles
        $vehicles = Vehicle::where('status', $status)
            ->with(['transports', 'vehicleInspections', 'salesTeam', 'assignedBy'])
            ->paginate(15);

        return response()->json($vehicles);
    }

    /**
     * Get vehicles filtered by status category
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function getVehiclesByCategory(Request $request)
    {
        $request->validate([
            'category' => 'required|string|in:available,transport,inspection,repair,sales,goodwill_claims,archive',
        ]);

        $category = $request->input('category');
        
        // Get the vehicles
        $vehicles = Vehicle::byStatusCategory($category)
            ->with(['transports', 'vehicleInspections', 'salesTeam', 'assignedBy'])
            ->paginate(15);

        return response()->json($vehicles);
    }
} 