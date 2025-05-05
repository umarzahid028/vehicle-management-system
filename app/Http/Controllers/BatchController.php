<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Transport;
use App\Models\Vehicle;
use App\Models\Transporter;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;

class BatchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Allow transporters to access their assigned batches
        $this->middleware(function ($request, $next) {
            if (auth()->user()->hasRole('Transporter')) {
                // Allow access to index, show, and updateStatus methods
                $allowedMethods = ['index', 'show', 'updateStatus'];
                if (!in_array($request->route()->getActionMethod(), $allowedMethods)) {
                    abort(403, 'Unauthorized action.');
                }

                // For show method, verify the batch belongs to the transporter
                if ($request->route()->getActionMethod() === 'show') {
                    $batchId = $request->route('batch');
                    if ($batchId) {
                        $batch = Batch::find($batchId);
                        if (!$batch || $batch->transporter_id !== auth()->user()->transporter_id) {
                            abort(403, 'Unauthorized access to this batch.');
                        }
                    }
                }
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the batches.
     */
    public function index(Request $request): View
    {
        $query = Batch::with(['transporter', 'transports.vehicle']);

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('batch_number', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('destination', 'like', "%{$search}%")
                  ->orWhereHas('transporter', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Date range filter
        if ($request->has('date_from') && $request->date_from != '') {
            $query->where('scheduled_pickup_date', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->where('scheduled_delivery_date', '<=', $request->date_to);
        }

        $batches = $query->latest()->paginate(10);
        return view('batches.index', compact('batches'));
    }

    /**
     * Show the form for creating a new batch.
     */
    public function create(): View
    {
        $transporters = Transporter::where('is_active', true)
                                  ->orderBy('name')
                                  ->get();
        
        $vehicles = Vehicle::whereDoesntHave('transports', function($query) {
                              $query->whereNotNull('batch_id');
                           })
                           ->where('status', '!=', 'sold')
                           ->orderBy('stock_number')
                           ->get();
                           
        return view('batches.create', compact('transporters', 'vehicles'));
    }

    /**
     * Store a newly created batch in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'transporter_id' => 'nullable|exists:transporters,id',
            'origin' => 'nullable|string|max:255',
            'destination' => 'required|string|max:255',
            'scheduled_pickup_date' => 'nullable|date',
            'scheduled_delivery_date' => 'nullable|date',
            'status' => 'required|string|in:pending,in_transit,delivered,cancelled',
            'notes' => 'nullable|string',
            'vehicle_ids' => 'required|array',
            'vehicle_ids.*' => 'exists:vehicles,id',
        ]);

        // Generate a unique batch number
        $batchNumber = 'B-' . date('ym') . '-' . strtoupper(Str::random(4));
        
        // Create the batch
        $batch = Batch::create([
            'batch_number' => $batchNumber,
            'name' => $validated['name'],
            'status' => $validated['status'],
            'transporter_id' => $validated['transporter_id'],
            'origin' => $validated['origin'],
            'destination' => $validated['destination'],
            'scheduled_pickup_date' => $validated['scheduled_pickup_date'],
            'scheduled_delivery_date' => $validated['scheduled_delivery_date'],
            'notes' => $validated['notes'],
        ]);
        
        // Create transport entries for each vehicle in the batch
        foreach ($validated['vehicle_ids'] as $vehicleId) {
            Transport::create([
                'vehicle_id' => $vehicleId,
                'batch_id' => $batch->id,
                'transporter_id' => $validated['transporter_id'],
                'origin' => $validated['origin'],
                'destination' => $validated['destination'],
                'pickup_date' => $validated['scheduled_pickup_date'],
                'delivery_date' => $validated['scheduled_delivery_date'],
                'status' => $validated['status'],
            ]);
            
            // Update vehicle transport status
            $vehicle = Vehicle::findOrFail($vehicleId);
            $vehicle->update(['transport_status' => $validated['status']]);
        }

        return redirect()->route('batches.show', $batch)
                         ->with('success', 'Batch created successfully.');
    }

    /**
     * Display the specified batch.
     */
    public function show(Batch $batch): View
    {
        $batch->load(['transporter', 'transports.vehicle', 'gatePasses']);
        return view('batches.show', compact('batch'));
    }

    /**
     * Show the form for editing the specified batch.
     */
    public function edit(Batch $batch): View
    {
        $batch->load(['transports.vehicle']);
        
        $transporters = Transporter::where('is_active', true)
                                  ->orderBy('name')
                                  ->get();
        
        $assignedVehicleIds = $batch->transports->pluck('vehicle_id')->toArray();
        
        $availableVehicles = Vehicle::whereDoesntHave('transports', function($query) use ($batch) {
                                $query->whereNotNull('batch_id')
                                      ->where('batch_id', '!=', $batch->id);
                             })
                             ->where('status', '!=', 'sold')
                             ->orderBy('stock_number')
                             ->get();
                             
        return view('batches.edit', compact('batch', 'transporters', 'availableVehicles', 'assignedVehicleIds'));
    }

    /**
     * Update the specified batch in storage.
     */
    public function update(Request $request, Batch $batch): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'transporter_id' => 'nullable|exists:transporters,id',
            'origin' => 'nullable|string|max:255',
            'destination' => 'required|string|max:255',
            'scheduled_pickup_date' => 'nullable|date',
            'scheduled_delivery_date' => 'nullable|date',
            'status' => 'required|string|in:pending,in_transit,delivered,cancelled',
            'notes' => 'nullable|string',
            'vehicle_ids' => 'required|array',
            'vehicle_ids.*' => 'exists:vehicles,id',
        ]);

        // Update the batch
        $batch->update([
            'name' => $validated['name'],
            'status' => $validated['status'],
            'transporter_id' => $validated['transporter_id'],
            'origin' => $validated['origin'],
            'destination' => $validated['destination'],
            'scheduled_pickup_date' => $validated['scheduled_pickup_date'],
            'scheduled_delivery_date' => $validated['scheduled_delivery_date'],
            'notes' => $validated['notes'],
        ]);
        
        // Get current vehicle IDs in the batch
        $currentVehicleIds = $batch->transports->pluck('vehicle_id')->toArray();
        
        // Find vehicles to remove and add
        $vehiclesToRemove = array_diff($currentVehicleIds, $validated['vehicle_ids']);
        $vehiclesToAdd = array_diff($validated['vehicle_ids'], $currentVehicleIds);
        
        // Remove vehicles no longer in the batch
        foreach ($vehiclesToRemove as $vehicleId) {
            Transport::where('vehicle_id', $vehicleId)
                    ->where('batch_id', $batch->id)
                    ->delete();
                    
            // Update vehicle transport status
            $vehicle = Vehicle::findOrFail($vehicleId);
            $vehicle->update(['transport_status' => null]);
        }
        
        // Add new vehicles to the batch
        foreach ($vehiclesToAdd as $vehicleId) {
            Transport::create([
                'vehicle_id' => $vehicleId,
                'batch_id' => $batch->id,
                'transporter_id' => $validated['transporter_id'],
                'origin' => $validated['origin'],
                'destination' => $validated['destination'],
                'pickup_date' => $validated['scheduled_pickup_date'],
                'delivery_date' => $validated['scheduled_delivery_date'],
                'status' => $validated['status'],
            ]);
            
            // Update vehicle transport status
            $vehicle = Vehicle::findOrFail($vehicleId);
            $vehicle->update(['transport_status' => $validated['status']]);
        }
        
        // Update status for all vehicles in the batch
        foreach ($validated['vehicle_ids'] as $vehicleId) {
            if (!in_array($vehicleId, $vehiclesToAdd)) {
                $transport = Transport::where('vehicle_id', $vehicleId)
                                    ->where('batch_id', $batch->id)
                                    ->first();
                                    
                if ($transport) {
                    $transport->update([
                        'transporter_id' => $validated['transporter_id'],
                        'origin' => $validated['origin'],
                        'destination' => $validated['destination'],
                        'pickup_date' => $validated['scheduled_pickup_date'],
                        'delivery_date' => $validated['scheduled_delivery_date'],
                        'status' => $validated['status'],
                    ]);
                }
                
                // Update vehicle transport status
                $vehicle = Vehicle::findOrFail($vehicleId);
                $vehicle->update(['transport_status' => $validated['status']]);
            }
        }

        return redirect()->route('batches.show', $batch)
                         ->with('success', 'Batch updated successfully.');
    }

    /**
     * Update batch status only.
     */
    public function updateStatus(Request $request, Batch $batch): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,in_transit,delivered,cancelled',
            'pickup_date' => 'nullable|date',
            'delivery_date' => 'nullable|date',
        ]);
        
        $updateData = ['status' => $validated['status']];
        
        if ($validated['status'] == 'in_transit' && $validated['pickup_date']) {
            $updateData['pickup_date'] = $validated['pickup_date'];
        }
        
        if ($validated['status'] == 'delivered' && $validated['delivery_date']) {
            $updateData['delivery_date'] = $validated['delivery_date'];
        }
        
        // Update the batch
        $batch->update($updateData);
        
        // Update all transports in the batch
        foreach ($batch->transports as $transport) {
            $transport->update([
                'status' => $validated['status'],
                'pickup_date' => $validated['pickup_date'] ?? $transport->pickup_date,
                'delivery_date' => $validated['delivery_date'] ?? $transport->delivery_date,
            ]);
            
            // Update vehicle transport status
            $transport->vehicle->update(['transport_status' => $validated['status']]);
        }
        
        return redirect()->route('batches.show', $batch)
                         ->with('success', 'Batch status updated successfully.');
    }

    /**
     * Remove the specified batch from storage.
     */
    public function destroy(Batch $batch): RedirectResponse
    {
        // Delete all transports in the batch
        foreach ($batch->transports as $transport) {
            // Reset vehicle transport status
            $transport->vehicle->update(['transport_status' => null]);
            $transport->delete();
        }
        
        $batch->delete();
        
        return redirect()->route('batches.index')
                         ->with('success', 'Batch removed successfully.');
    }
} 