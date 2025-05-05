<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\User;
use App\Models\VehicleInspection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SalesAssignmentController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:Admin|Sales Manager|Recon Manager');
    }
    
    /**
     * Display a listing of vehicles that are ready to be assigned to sales.
     */
    public function index()
    {
        // Get vehicles that are ready for sales assignment (repaired and ready)
        $readyVehicles = Vehicle::whereIn('status', [Vehicle::STATUS_READY_FOR_SALE, Vehicle::STATUS_REPAIRS_COMPLETED])
            ->whereHas('vehicleInspections', function($query) {
                $query->where('status', 'completed');
            })
            ->with(['vehicleInspections' => function($query) {
                $query->latest()->take(1);
            }])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
        
        // Get vehicles already assigned to sales
        $assignedVehicles = Vehicle::where('status', Vehicle::STATUS_ASSIGNED_TO_SALES)
            ->with(['salesTeam', 'assignedBy'])
            ->orderBy('assigned_for_sale_at', 'desc')
            ->paginate(10);
            
        // Get sales team members (users with Sales Team role)
        $salesTeamMembers = User::role('Sales Team')->orderBy('name')->get();
        
        // Check if we have any sales team members
        if ($salesTeamMembers->isEmpty() && $readyVehicles->isNotEmpty()) {
            session()->flash('warning', 'No sales team members are available. Please add sales team members before assigning vehicles.');
        }
            
        return view('sales-assignments.index', compact('readyVehicles', 'assignedVehicles', 'salesTeamMembers'));
    }
    
    /**
     * Show the form for assigning a vehicle to sales team.
     */
    public function create(Request $request, $vehicleId = null)
    {
       
        try {
            // Check if we're receiving a direct vehicle ID parameter
            if ($vehicleId ) {
                $vehicle = Vehicle::findOrFail($vehicleId);
                
            }
            // Otherwise, check if a 'vehicle' parameter was passed in the request
            else if ($request->has('vehicle') && ($request->vehicle)) {
                $vehicle = Vehicle::findOrFail($request->vehicle);
            }
            // Fallback for any other ways the vehicle might be passed
            else {
                $vehicle = $request->vehicle instanceof Vehicle ? $request->vehicle : null;
            }
            
            // First verify that we have a valid vehicle with an ID
            if (!$vehicle || !$vehicle->id) {
              
                return redirect()->route('sales-assignments.index')
                    ->with('error', 'Invalid vehicle. Please try again with a valid vehicle.');
            }

            // Check if the vehicle exists and is in a status that can be assigned to sales
            if (!$vehicle || !in_array($vehicle->status, [
                Vehicle::STATUS_READY_FOR_SALE,
            ])) {
                return redirect()->route('sales-assignments.index')
                    ->with('error', 'Invalid vehicle. Please try again with a valid vehicle.');
            }

            // Check that the vehicle has a status
            if (!$vehicle->status) {
                
                $reloadedVehicle = Vehicle::find($vehicle->id);
                if (!$reloadedVehicle || !$reloadedVehicle->status) {
                    return redirect()->route('sales-assignments.index')
                        ->with('error', 'Vehicle has no status assigned. Please ensure the vehicle record is complete.');
                }
                
                // If we found a status on reload, use the reloaded vehicle
                $vehicle = $reloadedVehicle;
            }

            // Now check if vehicle is ready for assignment using our new hasStatus method
            $validStatuses = [
                Vehicle::STATUS_READY_FOR_SALE, 
                Vehicle::STATUS_REPAIRS_COMPLETED
            ];
            
            if (!$vehicle->hasStatus($validStatuses)) {
                return redirect()->route('sales-assignments.index')
                    ->with('error', "This vehicle is not ready for sales assignment. Current status: {$vehicle->status}");
            }
            
            // Get completed inspection
            $completedInspection = $vehicle->vehicleInspections()
                ->where('status', 'completed')
                ->latest()
                ->first();
                
            // Check if the vehicle has completed inspections
            if (!$completedInspection) {
                return redirect()->route('sales-assignments.index')
                    ->with('error', 'This vehicle does not have a completed inspection.');
            }
            
            // Get sales team members
            $salesTeamMembers = User::role('Sales Team')->orderBy('name')->get();
            
            if ($salesTeamMembers->isEmpty()) {
                return redirect()->route('sales-assignments.index')
                    ->with('error', 'No sales team members available for assignment.');
            }
           
            return view('sales-assignments.create', compact('vehicle', 'salesTeamMembers', 'completedInspection'));
        } catch (\Exception $e) {
            return redirect()->route('sales-assignments.index')
                ->with('error', 'An error occurred when trying to assign the vehicle to sales team: ' . $e->getMessage());
        }
    }
    
    /**
     * Store a newly created sales assignment.
     */
    public function store(Request $request, Vehicle $vehicle)
    {
        try {
            
            
            // Validate request
            $validated = $request->validate([
                'sales_team_id' => 'required|exists:users,id',
                'notes' => 'nullable|string|max:500',
            ]);
            
            // Verify the vehicle exists and has a status
            if (!$vehicle || !$vehicle->id || !$vehicle->status) {
               
                return redirect()->route('sales-assignments.index')
                    ->with('error', 'Invalid vehicle or missing vehicle status. Please try again.');
            }
            
            // Check if vehicle is ready for assignment
            $validStatuses = [
                Vehicle::STATUS_READY_FOR_SALE, 
                Vehicle::STATUS_REPAIRS_COMPLETED
            ];
          
            if (!$vehicle->hasStatus($validStatuses)) {
                
                return redirect()->route('sales-assignments.index')
                    ->with('error', "This vehicle is not ready for sales assignment. Current status: {$vehicle->status}");
            }
            
            // Check if the selected user has Sales Team role
            $salesTeamMember = User::findOrFail($validated['sales_team_id']);
            if (!$salesTeamMember->hasRole('Sales Team')) {
                return redirect()->route('sales-assignments.create', $vehicle)
                    ->with('error', 'The selected user is not a member of the sales team.');
            }
            
            DB::beginTransaction();
            
            try {
                // Assign the vehicle to sales team
                $vehicle->assignToSalesTeam($salesTeamMember->id, Auth::id());
                
                // Add notes if provided
                if (!empty($validated['notes'])) {
                    // You can implement notes functionality here if needed
                    // Example: $vehicle->notes()->create(['content' => $validated['notes'], 'user_id' => Auth::id()]);
                }

                
                
                DB::commit();
                
           
                return redirect()->route('sales-assignments.index')
                    ->with('success', "Vehicle {$vehicle->stock_number} assigned to {$salesTeamMember->name} successfully.");
                    
            } catch (\Exception $e) {
                DB::rollBack();
                
                return redirect()->route('sales-assignments.create', $vehicle)
                    ->with('error', 'Failed to assign vehicle to sales team: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            
            return redirect()->route('sales-assignments.index')
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }
    
    /**
     * Show details of a specific sales assignment.
     */
    public function show(Vehicle $vehicle)
    {
        // Check if vehicle is assigned to sales
        if ($vehicle->status !== "Assigned to Sales") {
            return redirect()->route('sales-assignments.index')
                ->with('error', 'This vehicle is not assigned to sales.');
        }
        
        // Load relations
        $vehicle->load(['salesTeam', 'assignedBy', 'vehicleInspections' => function($query) {
            $query->latest()->take(1);
        }]);
        
        return view('sales-assignments.show', compact('vehicle'));
    }
    
    /**
     * Remove a sales assignment.
     */
    public function destroy(Vehicle $vehicle)
    {
        // Check if vehicle is assigned to sales
        if ($vehicle->status !== "Assigned to Sales") {
            return redirect()->route('sales-assignments.index')
                ->with('error', 'This vehicle is not assigned to sales.');
        }
        
        // Reset the vehicle status to ready
        $vehicle->update([
            'status' => Vehicle::STATUS_READY_FOR_SALE,
            'sales_team_id' => null,
            'assigned_for_sale_by' => null,
            'assigned_for_sale_at' => null,
        ]);
        
        return redirect()->route('sales-assignments.index')
            ->with('success', "Sales assignment for vehicle {$vehicle->stock_number} has been removed.");
    }
    
    /**
     * Debug method for sales assignments.
     */
    public function debug()
    {
        $readyVehicles = Vehicle::whereIn('status', [Vehicle::STATUS_READY_FOR_SALE, Vehicle::STATUS_REPAIRS_COMPLETED])
            ->whereHas('vehicleInspections', function($query) {
                $query->where('status', 'completed');
            })
            ->with(['vehicleInspections' => function($query) {
                $query->latest()->take(1);
            }])
            ->orderBy('updated_at', 'desc')
            ->get();
        
        $data = [
            'ready_vehicles' => $readyVehicles->map(function($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'stock_number' => $vehicle->stock_number,
                    'status' => $vehicle->status,
                    'inspections' => $vehicle->vehicleInspections->map(function($inspection) {
                        return [
                            'id' => $inspection->id,
                            'status' => $inspection->status,
                            'completed_date' => $inspection->completed_date,
                        ];
                    }),
                ];
            }),
        ];
        
        return response()->json($data);
    }
}
