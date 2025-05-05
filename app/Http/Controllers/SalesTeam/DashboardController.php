<?php

namespace App\Http\Controllers\SalesTeam;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display the sales team dashboard with vehicles ready for sale.
     */
    public function index()
    {
        $readyVehicles = Vehicle::query()
            ->where('status', 'Ready for Sale')
            ->with(['make', 'model', 'photos', 'inspections', 'salesIssues'])
            ->latest()
            ->paginate(10);

        $stats = [
            'total_ready' => Vehicle::where('status', 'Ready for Sale')->count(),
            'total_with_issues' => Vehicle::where('status', 'Ready for Sale')
                ->whereHas('salesIssues', function($query) {
                    $query->whereNull('resolved_at');
                })->count(),
            'total_sold' => Vehicle::where('status', 'Sold')->count(),
        ];

        return view('sales-team.dashboard', compact('readyVehicles', 'stats'));
    }
    
} 