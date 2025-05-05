<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Transport;
use App\Models\Vehicle;
use App\Models\VendorEstimate;
use App\Models\Sale;
use Carbon\Carbon;
use App\Models\Estimate;
use Illuminate\Support\Facades\DB;
use App\Models\Inspection;
use App\Models\SalesIssue;
use App\Models\Activity;
use App\Models\VehicleInspection;
use App\Models\Vendor;
use App\Models\InspectionItemResult;
use App\Models\User;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Get current month and last month dates
        $now = Carbon::now();
        $currentMonth = $now->format('Y-m');
        $lastMonth = $now->copy()->subMonth()->format('Y-m');
        
        // Total vehicles and growth rate
        $totalVehicles = Vehicle::count();
        $lastMonthVehicleCount = Vehicle::where('created_at', '<', $now->copy()->startOfMonth())->count();
        $vehicleGrowth = $lastMonthVehicleCount > 0
            ? round((($totalVehicles - $lastMonthVehicleCount) / $lastMonthVehicleCount) * 100, 1)
            : 0;
            
        // Inspection metrics
        $activeInspections = VehicleInspection::where('status', '!=', 'completed')->count();
        $completedInspections = VehicleInspection::where('status', 'completed')->count();
        
        // Open issues metrics
        $openIssues = SalesIssue::where('status', 'open')->count();
        $resolvedIssues = SalesIssue::where('status', 'resolved')->count();
        
        // Monthly revenue
        $monthlyRevenue = Sale::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->sum('amount');
            
        $lastMonthRevenue = Sale::whereYear('created_at', $now->copy()->subMonth()->year)
            ->whereMonth('created_at', $now->copy()->subMonth()->month)
            ->sum('amount');
        
        // Vehicle status distribution for chart
        $vehicleStatusChart = Vehicle::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
            
        // Revenue chart - last 6 months
        $revenueChart = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = $now->copy()->subMonths($i);
            $revenue = Sale::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('amount');
            
            $revenueChart[$month->format('M Y')] = $revenue;
        }
        
        // Recent activities
        $recentActivities = Activity::with('user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
            
        // Calculate repair metrics
        $repairsNeeded = InspectionItemResult::where('requires_repair', true)->count();
        $repairsCompleted = InspectionItemResult::where('requires_repair', true)
            ->where('repair_completed', true)
            ->count();
        
        $repairCompletionRate = $repairsNeeded > 0 
            ? round(($repairsCompleted / $repairsNeeded) * 100) 
            : 0;
            
        // Top performing sales staff if relevant
        $topSalesStaff = null;
        if (class_exists('App\Models\SalesTeam')) {
            $topSalesStaff = DB::table('sales')
                ->join('users', 'sales.user_id', '=', 'users.id')
                ->select('users.name', DB::raw('COUNT(*) as sales_count'), DB::raw('SUM(amount) as total_sales'))
                ->whereYear('sales.created_at', $now->year)
                ->whereMonth('sales.created_at', $now->month)
                ->groupBy('users.id', 'users.name')
                ->orderBy('total_sales', 'desc')
                ->limit(3)
                ->get();
        }
        
        return view('dashboard', compact(
            'totalVehicles',
            'vehicleGrowth',
            'activeInspections',
            'completedInspections',
            'openIssues',
            'resolvedIssues',
            'monthlyRevenue',
            'lastMonthRevenue',
            'vehicleStatusChart',
            'revenueChart',
            'recentActivities',
            'repairCompletionRate',
            'topSalesStaff'
        ));
    }

    /**
     * Dashboard for Sales Manager and Recon Manager.
     */
    protected function managerDashboard(): View
    {
        $today = Carbon::today();
        $thisMonth = Carbon::now()->startOfMonth();

        // Get sales data
        $salesData = Sale::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
            ->where('created_at', '>=', $thisMonth)
            ->groupBy('date')
            ->get();

        // Get vehicle data
        $vehicleStats = [
            'total' => Vehicle::count(),
            'in_stock' => Vehicle::where('status', 'in_stock')->count(),
            'in_recon' => Vehicle::where('status', 'in_recon')->count(),
            'sold' => Vehicle::where('status', 'sold')->count(),
        ];

        // Get transport data
        $transportStats = [
            'pending' => Transport::where('status', 'pending')->count(),
            'in_transit' => Transport::where('status', 'in_transit')->count(),
            'delivered' => Transport::where('status', 'delivered')->count(),
        ];

        // Get vendor estimates data
        $estimatesData = VendorEstimate::selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(estimated_cost) as total')
            ->where('created_at', '>=', $thisMonth)
            ->groupBy('date')
            ->get();

        // Get recent inspections
        $recentInspections = VehicleInspection::with('vehicle')
            ->latest()
            ->take(5)
            ->get();

        // Get repair statistics
        $repairStats = [
            'needs_repair' => Vehicle::where('status', 'needs_repair')->count(),
            'repair_assigned' => Vehicle::where('status', 'repair_assigned')->count(),
            'total_cost' => VehicleInspection::sum('total_cost'),
            'avg_repair_time' => $this->calculateAverageRepairTime()
        ];

        // Get sales team performance
        $salesPerformance = $this->getSalesTeamPerformance();

        // Get inventory aging data
        $inventoryAging = $this->getInventoryAging();

        // Get vendor performance metrics
        $vendorPerformance = $this->getVendorPerformance();

        return view('dashboards.manager', compact(
            'salesData',
            'vehicleStats',
            'transportStats',
            'estimatesData',
            'recentInspections',
            'repairStats',
            'salesPerformance',
            'inventoryAging',
            'vendorPerformance'
        ));
    }

    /**
     * Calculate the average repair time in days.
     */
    private function calculateAverageRepairTime(): int
    {
        $completedRepairs = VehicleInspection::whereNotNull('completed_date')
            ->whereHas('itemResults', function($query) {
                $query->where('requires_repair', true)
                      ->where('repair_completed', true);
            })
            ->get();
            
        if ($completedRepairs->isEmpty()) {
            return 0;
        }
        
        $totalDays = 0;
        $count = 0;
        
        foreach ($completedRepairs as $repair) {
            $results = $repair->itemResults()
                ->where('requires_repair', true)
                ->where('repair_completed', true)
                ->whereNotNull('completed_at')
                ->get();
                
            foreach ($results as $result) {
                $created = new Carbon($repair->created_at);
                $completed = new Carbon($result->completed_at);
                $days = $created->diffInDays($completed);
                
                $totalDays += $days;
                $count++;
            }
        }
        
        return $count > 0 ? round($totalDays / $count) : 0;
    }

    /**
     * Get sales team performance data.
     */
    private function getSalesTeamPerformance(): array
    {
        $thisMonth = Carbon::now()->startOfMonth();
        
        $salesData = DB::table('sales')
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->where('sales.created_at', '>=', $thisMonth)
            ->select(
                'users.name',
                DB::raw('COUNT(sales.id) as sales_count'),
                DB::raw('SUM(sales.amount) as sales_amount')
            )
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('sales_amount')
            ->limit(5)
            ->get();
            
        return $salesData->map(function($item) {
            return [
                'name' => $item->name,
                'sales_count' => $item->sales_count,
                'sales_amount' => $item->sales_amount
            ];
        })->toArray();
    }

    /**
     * Get inventory aging data.
     */
    private function getInventoryAging(): array
    {
        $now = Carbon::now();
        $days30 = $now->copy()->subDays(30);
        $days60 = $now->copy()->subDays(60);
        $days90 = $now->copy()->subDays(90);
        
        // Only count vehicles that are in stock or in recon
        $vehicles = Vehicle::whereIn('status', ['in_stock', 'in_recon', 'ready', 'needs_repair', 'repair_assigned'])
            ->get();
            
        $total = $vehicles->count();
        $totalDays = 0;
        
        if ($total === 0) {
            return [
                '0_30' => 0,
                '31_60' => 0,
                '61_90' => 0,
                '90_plus' => 0,
                'avg_days' => 0
            ];
        }
        
        $aging = [
            '0_30' => 0,
            '31_60' => 0,
            '61_90' => 0,
            '90_plus' => 0
        ];
        
        foreach ($vehicles as $vehicle) {
            $created = new Carbon($vehicle->created_at);
            $days = $created->diffInDays($now);
            $totalDays += $days;
            
            if ($created->gt($days30)) {
                $aging['0_30']++;
            } elseif ($created->gt($days60)) {
                $aging['31_60']++;
            } elseif ($created->gt($days90)) {
                $aging['61_90']++;
            } else {
                $aging['90_plus']++;
            }
        }
        
        $aging['avg_days'] = round($totalDays / $total);
        
        return $aging;
    }

    /**
     * Get vendor performance metrics.
     */
    private function getVendorPerformance(): array
    {
        $vendors = Vendor::has('inspectionItemResults')
            ->withCount('inspectionItemResults as total_jobs')
            ->with(['inspectionItemResults' => function($query) {
                $query->with('repairImages');
            }])
            ->get();
            
        return $vendors->map(function($vendor) {
            $completedJobs = $vendor->inspectionItemResults->where('repair_completed', true)->count();
            $totalJobs = $vendor->total_jobs;
            $completionRate = $totalJobs > 0 ? round(($completedJobs / $totalJobs) * 100) : 0;
            
            $totalDays = 0;
            $completedCount = 0;
            $totalCost = 0;
            
            foreach ($vendor->inspectionItemResults as $result) {
                $totalCost += $result->cost;
                
                if ($result->repair_completed && $result->completed_at) {
                    $created = new Carbon($result->created_at);
                    $completed = new Carbon($result->completed_at);
                    $days = $created->diffInDays($completed);
                    
                    $totalDays += $days;
                    $completedCount++;
                }
            }
            
            return [
                'name' => $vendor->name,
                'total_jobs' => $totalJobs,
                'completion_rate' => $completionRate,
                'avg_days' => $completedCount > 0 ? round($totalDays / $completedCount) : 0,
                'total_cost' => $totalCost
            ];
        })
        ->sortByDesc('total_jobs')
        ->take(5)
        ->values()
        ->toArray();
    }

    /**
     * Dashboard for Transporters.
     */
    protected function transporterDashboard(): View
    {
        $transporter_id = auth()->user()->transporter_id;
        $currentYear = now()->year;

        // Get transport statistics
        $transportStats = [
            'pending' => Transport::where('transporter_id', $transporter_id)
                ->where('status', 'pending')
                ->count(),
            'in_transit' => Transport::where('transporter_id', $transporter_id)
                ->where('status', 'in_transit')
                ->count(),
            'delivered' => Transport::where('transporter_id', $transporter_id)
                ->where('status', 'delivered')
                ->count(),
        ];

        // Check if any data exists for the year and this transporter
        $hasData = Transport::where('transporter_id', $transporter_id)
            ->whereYear('created_at', $currentYear)
            ->exists();
            
        if ($hasData) {
            // Get monthly transport data directly 
            $monthlyData = Transport::where('transporter_id', $transporter_id)
                ->whereYear('created_at', $currentYear)
                ->selectRaw('MONTH(created_at) as month, COUNT(*) as count')
                ->groupBy('month')
                ->orderBy('month')
                ->get();
                
            // Initialize array with zeros for all months (0-11 for Jan-Dec)
            $monthlyTransports = array_fill(0, 12, 0);
            
            // Fill in the actual counts
            foreach ($monthlyData as $data) {
                $monthlyTransports[$data->month - 1] = (int)$data->count;
            }
        } else {
            // Provide sample data for demonstration if no real data exists
            $monthlyTransports = [2, 4, 1, 5, 3, 2, 6, 4, 3, 2, 1, 2];
        }
        
        // Get recent transport activities
        $recentActivities = Transport::where('transporter_id', $transporter_id)
            ->with(['vehicle', 'transporter'])
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('dashboards.transporter', compact('transportStats', 'monthlyTransports', 'recentActivities'));
    }

    /**
     * Dashboard for Vendors.
     */
    protected function vendorDashboard()
    {
        return redirect()->route('vendor.dashboard');
    }

    /**
     * Dashboard for Sales Team.
     */
    protected function salesTeamDashboard(): View
    {
        $user_id = auth()->id();
        $thisMonth = Carbon::now()->startOfMonth();

        // Get sales statistics
        $salesStats = Sale::where('user_id', $user_id)
            ->where('created_at', '>=', $thisMonth)
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(amount) as total')
            ->groupBy('date')
            ->get();

        // Get performance metrics
        $performance = [
            'total_sales' => Sale::where('user_id', $user_id)->count(),
            'monthly_sales' => Sale::where('user_id', $user_id)
                ->where('created_at', '>=', $thisMonth)
                ->count(),
            'total_amount' => Sale::where('user_id', $user_id)
                ->sum('amount'),
            'monthly_amount' => Sale::where('user_id', $user_id)
                ->where('created_at', '>=', $thisMonth)
                ->sum('amount'),
        ];

        return view('dashboards.sales', compact('salesStats', 'performance'));
    }

    public function vendor()
    {
        // Redirect to the dedicated vendor dashboard controller
        return redirect()->route('vendor.dashboard');
    }

    public function transporter()
    {
        // Get transport statistics
        $transportStats = [
            'pending' => Transport::where('status', 'pending')->count(),
            'in_transit' => Transport::where('status', 'in_transit')->count(),
            'delivered' => Transport::where('status', 'delivered')->count()
        ];

        // Get recent transport activities
        $recentActivities = Transport::with('vehicle')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($transport) {
                return (object)[
                    'id' => $transport->id,
                    'status' => $transport->status,
                    'vehicle' => (object)[
                        'make' => $transport->vehicle->make,
                        'model' => $transport->vehicle->model,
                        'stock_number' => $transport->vehicle->stock_number
                    ],
                    'origin' => $transport->origin,
                    'destination' => $transport->destination,
                    'updated_at' => $transport->updated_at,
                    'pickup_date' => $transport->pickup_date,
                    'delivery_date' => $transport->delivery_date
                ];
            });

        return view('dashboards.transporter', compact(
            'transportStats',
            'recentActivities'
        ));
    }

    public function manager()
    {
        // Get vehicle statistics
        $vehicleStats = [
            'total' => Vehicle::count(),
            'in_stock' => Vehicle::where('status', 'in_stock')->count(),
            'in_recon' => Vehicle::where('status', 'in_recon')->count(),
            'sold' => Vehicle::where('status', 'sold')->count()
        ];

        // Get transport statistics
        $transportStats = [
            'pending' => Transport::where('status', 'pending')->count(),
            'in_transit' => Transport::where('status', 'in_transit')->count(),
            'delivered' => Transport::where('status', 'delivered')->count()
        ];

        // Get monthly sales data for the last 6 months
        $salesData = Sale::select(
            DB::raw('DATE_FORMAT(created_at, "%b") as date'),
            DB::raw('SUM(total_amount) as total'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('date')
            ->orderBy('created_at')
            ->get();

        // Get monthly estimates data for the last 6 months
        $estimatesData = Estimate::select(
            DB::raw('DATE_FORMAT(created_at, "%b") as date'),
            DB::raw('SUM(total_amount) as total'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('date')
            ->orderBy('created_at')
            ->get();

        return view('dashboards.manager', compact(
            'vehicleStats',
            'transportStats',
            'salesData',
            'estimatesData'
        ));
    }

    public function __invoke(Request $request)
    {
        // Get date range
        $days = $request->get('days', 30);
        $startDate = Carbon::now()->subDays($days);
        $endDate = Carbon::now();

        // Vehicle Statistics
        $totalVehicles = Vehicle::count();
        $lastMonthVehicles = Vehicle::where('created_at', '<', Carbon::now()->startOfMonth())->count();
        $vehicleGrowth = $lastMonthVehicles > 0 
            ? round((($totalVehicles - $lastMonthVehicles) / $lastMonthVehicles) * 100, 1)
            : 0;

        // Inspection Statistics
        $activeInspections = Inspection::where('status', 'in_progress')->count();
        $completedInspections = Inspection::where('status', 'completed')
            ->whereDate('completed_at', Carbon::today())
            ->count();

        // Issues Statistics
        $openIssues = SalesIssue::where('status', 'open')->count();
        $resolvedIssues = SalesIssue::where('status', 'resolved')
            ->whereBetween('resolved_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
            ->count();

        // Revenue Statistics
        $monthlyRevenue = DB::table('sales')
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('amount');
        
        $lastMonthRevenue = DB::table('sales')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->sum('amount');
        
        $revenueGrowth = $lastMonthRevenue > 0 
            ? round((($monthlyRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1)
            : 0;

        // Vehicle Status Chart Data
        $vehicleStatusData = Vehicle::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $vehicleStatusLabels = array_map(function($status) {
            return ucfirst(str_replace('_', ' ', $status));
        }, array_keys($vehicleStatusData));

        // Revenue Chart Data
        $revenueData = DB::table('sales')
            ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('SUM(amount) as total'))
            ->whereBetween('created_at', [Carbon::now()->subMonths(5), Carbon::now()])
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        $revenueLabels = array_map(function($month) {
            return Carbon::createFromFormat('Y-m', $month)->format('M Y');
        }, array_keys($revenueData));

        // Recent Activities
        $recentActivities = Activity::with('user')
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalVehicles',
            'vehicleGrowth',
            'activeInspections',
            'completedInspections',
            'openIssues',
            'resolvedIssues',
            'monthlyRevenue',
            'revenueGrowth',
            'vehicleStatusData',
            'vehicleStatusLabels',
            'revenueData',
            'revenueLabels',
            'recentActivities'
        ));
    }
} 