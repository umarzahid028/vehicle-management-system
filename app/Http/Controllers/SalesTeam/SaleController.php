<?php

namespace App\Http\Controllers\SalesTeam;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use App\Http\Requests\StoreSaleRequest;

class SaleController extends Controller
{
    public function create(Request $request)
    {
        // Get the authenticated user
        $user = auth()->user();
       
    
        // Get all vehicles assigned to this sales team member
        $availableVehicles = Vehicle::where('sales_team_id', $user->id)
            ->where('status', Vehicle::STATUS_ASSIGNED_TO_SALES)
            ->get();
       // dd($availableVehicles);
        // Pre-select vehicle if vehicle_id is provided in the request
        $selectedVehicleId = $request->input('vehicle_id');
        
        return view('sales-team.sales.create', compact('availableVehicles', 'selectedVehicleId'));
    }
    
    public function store(StoreSaleRequest $request)
    {
      
        // Get the authenticated user
        $user = auth()->user();
        
        // Check if user has a salesTeam associated with them
        if (!$user->salesTeam) {
            dd($user);
            return redirect()->route('dashboard')
                ->with('error', 'Your account is not properly linked to a sales team. Please contact an administrator.');
        }
        
        // Validate that the vehicle belongs to the user's sales team
        $vehicle = Vehicle::findOrFail($request->vehicle_id);
        
        if ($vehicle->sales_team_id !== $user->id) {
            return back()->withErrors(['vehicle_id' => 'You can only create sales for vehicles assigned to your team.']);
        }
        
        // Create the sale
        $sale = Sale::create([
            'vehicle_id' => $request->vehicle_id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'amount' => $request->amount,
            'sale_date' => now(),
            'user_id' => $user->id,
            'sales_team_id' => $user->salesTeam->id,
            // Add any other required fields
        ]);
        
        // Update vehicle status
        $vehicle->update(['status' => Vehicle::STATUS_SOLD]);
        
        return redirect()->route('sales-team.vehicles.index')
            ->with('success', 'Sale has been created successfully.');
    }

    
} 