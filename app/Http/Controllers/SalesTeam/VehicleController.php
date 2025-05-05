<?php

namespace App\Http\Controllers\SalesTeam;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;

class VehicleController extends Controller
{
    public function index()
    {
      
        // Get the authenticated user's sales team
        $id = auth()->user()->id;
        // Fetch vehicles assigned to this sales team
        $vehicles = Vehicle::with('sale')->where('sales_team_id', $id)
            // ->where('status', 'available')
            ->paginate(10);
            
        return view('sales-team.vehicles.index', compact('vehicles'));
    }
} 