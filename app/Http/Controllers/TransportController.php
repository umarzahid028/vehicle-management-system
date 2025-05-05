<?php

namespace App\Http\Controllers;

use App\Models\Transport;
use App\Models\Vehicle;
use App\Models\Transporter;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
use App\Models\Batch;

class TransportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // Only allow transporters to access index and show methods
        $this->middleware(function ($request, $next) {
            if (auth()->user()->hasRole('Transporter')) {
                //dd($request->route()->getActionMethod());
                if (!in_array($request->route()->getActionMethod(), [
                    'index', 
                    'show', 
                    'showBatch', 
                    'acknowledge', 
                    'acknowledgeBatch',
                    'updateBatchStatus', 
                    'updateTransportStatus'
                ])) {
                    abort(403, 'Unauthorized action.');
                }
            }
            return $next($request);
        });
    }

    /**
     * Display a listing of the transports.
     */
    public function index(Request $request): View
    {
        $query = Transport::query()
            ->select([
                'batch_id',
                DB::raw('MIN(id) as id'), // Select one ID per batch for pagination
                DB::raw('COUNT(DISTINCT vehicle_id) as vehicle_count'),
                DB::raw('MIN(origin) as batch_origin'),
                DB::raw('MIN(destination) as batch_destination'),
                DB::raw('MIN(pickup_date) as batch_pickup_date'),
                DB::raw('MIN(delivery_date) as batch_delivery_date'),
                DB::raw('MIN(transporter_id) as transporter_id'),
                DB::raw('MIN(transporter_name) as batch_transporter_name'),
                DB::raw('MIN(transporter_phone) as transporter_phone'),
                DB::raw('MIN(status) as batch_status'),
                DB::raw('MIN(created_at) as batch_created_at'),
                DB::raw('MIN(updated_at) as batch_updated_at'),
                DB::raw('MIN(batch_name) as batch_name'),
                DB::raw('CASE WHEN COUNT(*) = SUM(CASE WHEN is_acknowledged = 1 THEN 1 ELSE 0 END) THEN 1 ELSE 0 END as is_acknowledged'),
                DB::raw('MAX(acknowledged_at) as acknowledged_at'),
                DB::raw('MAX(acknowledged_by) as acknowledged_by')
            ])
            ->groupBy('batch_id');

        // Filter transports based on user role
        if (auth()->user()->hasRole('Transporter')) {
            $query->where('transporter_id', auth()->user()->transporter_id);
        }

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('vehicle', function ($vq) use ($search) {
                    $vq->where('stock_number', 'like', "%{$search}%")
                      ->orWhere('vin', 'like', "%{$search}%")
                      ->orWhere('make', 'like', "%{$search}%")
                      ->orWhere('model', 'like', "%{$search}%");
                })
                ->orWhere('transporter_name', 'like', "%{$search}%")
                ->orWhere('destination', 'like', "%{$search}%")
                ->orWhere('batch_id', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->having('batch_status', $request->status);
        }

        // Filter by acknowledgment
        if ($request->has('acknowledged') && $request->acknowledged != '') {
            $isAcknowledged = $request->acknowledged === 'true';
            $query->having('is_acknowledged', $isAcknowledged);
        }

        $transports = $query->latest('batch_created_at')->paginate(10);

        return view('transports.index', compact('transports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        if (auth()->user()->hasRole('Transporter')) {
           abort(403, 'Unauthorized action.');
        }
        
        $this->authorize('create transports');
        
        // Get vehicles that are not sold and either have no transport or their transport is cancelled
        $vehicles = Vehicle::where('status', '!=', 'sold')
                          ->where(function($query) {
                              $query->whereDoesntHave('transports', function($q) {
                                        $q->where('status', '!=', 'cancelled');
                                    })
                                    ->orWhereHas('transports', function($q) {
                                        $q->where('status', 'cancelled');
                                    });
                          })
                          ->orderBy('stock_number')
                          ->get();
                          
        $transporters = Transporter::where('is_active', true)
                                 ->orderBy('name')
                                 ->get();
        return view('transports.create', compact('vehicles', 'transporters'));
    }

    /**
     * Show the form for creating a new batch transport.
     */
    public function createBatch(): View
    {
        if (auth()->user()->hasRole('Transporters')) {
            abort(403, 'Unauthorized action.');
        }
        
        $this->authorize('create transports');
        
        // Get vehicles that are not sold and either have no transport or their transport is cancelled
        $vehicles = Vehicle::where('status', '!=', 'sold')
                          ->where(function($query) {
                              $query->whereDoesntHave('transports', function($q) {
                                        $q->where('status', '!=', 'cancelled');
                                    })
                                    ->orWhereHas('transports', function($q) {
                                        $q->where('status', 'cancelled');
                                    });
                          })
                          ->orderBy('stock_number')
                          ->get();
                          
        $transporters = Transporter::where('is_active', true)
                                 ->orderBy('name')
                                 ->get();
        return view('transports.batch.create', compact('vehicles', 'transporters'));
    }

    /**
     * Store a newly created transport in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()->hasRole('Transporter')) {
            abort(403, 'Unauthorized action.');
        }
        
        $this->authorize('create transports');
        $validated = $request->validate([
            'vehicle_ids' => 'required|array|min:1',
            'vehicle_ids.*' => 'exists:vehicles,id',
            'transporter_id' => 'nullable|exists:transporters,id',
            'origin' => 'nullable|string|max:255',
            'destination' => 'required|string|max:255',
            'pickup_date' => 'nullable|date',
            'delivery_date' => 'nullable|date',
            'status' => 'required|string|in:pending,in_transit,delivered,cancelled',
            'transporter_name' => 'nullable|string|max:255',
            'transporter_phone' => 'nullable|string|max:255',
            'transporter_email' => 'nullable|string|email|max:255',
            'notes' => 'nullable|string',
            'batch_name' => 'nullable|string|max:255',
            'generate_qr' => 'nullable|boolean',
            'gate_passes' => 'nullable|array',
            'gate_passes.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // If transporter_id is selected, clear manual transporter fields
        if (!empty($validated['transporter_id'])) {
            $validated['transporter_name'] = null;
            $validated['transporter_phone'] = null;
            $validated['transporter_email'] = null;
        }

        // Generate batch number
        $batchNumber = 'B-' . date('ymd') . '-' . strtoupper(Str::random(4));
        
        // Get common transport data
        $transportData = [
            'batch_id' => $batchNumber,
            'origin' => $validated['origin'],
            'destination' => $validated['destination'],
            'pickup_date' => $validated['pickup_date'],
            'delivery_date' => $validated['delivery_date'],
            'status' => $validated['status'],
            'transporter_id' => $validated['transporter_id'],
            'transporter_name' => $validated['transporter_name'],
            'transporter_phone' => $validated['transporter_phone'],
            'transporter_email' => $validated['transporter_email'],
            'notes' => $validated['notes'],
            'batch_name' => $validated['batch_name'],
        ];

        // Generate QR Code if requested
        if ($request->generate_qr) {
            // Create full absolute URL for the tracking page
            $qrUrl = url("/track/{$batchNumber}");
            $qrPath = 'qrcodes/' . $batchNumber . '.png';
            $qrCode = QrCode::format('png')->size(300)->generate($qrUrl);
            Storage::disk('public')->put($qrPath, $qrCode);
            $transportData['qr_code_path'] = $qrPath;
        }

        // Create a transport entry for each selected vehicle
        foreach ($validated['vehicle_ids'] as $vehicleId) {
            $vehicle = Vehicle::findOrFail($vehicleId);
            
            // Create transport record
            $transport = new Transport($transportData);
            $transport->vehicle_id = $vehicleId;
            $transport->save();
            
            // Handle gate pass upload if provided
            if ($request->hasFile("gate_passes.{$vehicleId}")) {
                $file = $request->file("gate_passes.{$vehicleId}");
                $path = $file->store('gate-passes', 'public');
                $transport->gate_pass_path = $path;
                $transport->save();
            }
            
            // Update vehicle transport status
            if ($request->status == 'in_transit') {
                $vehicle->update(['transport_status' => 'in_transit']);
            }
        }

        return redirect()->route('transports.index')
                         ->with('success', count($validated['vehicle_ids']) . ' vehicles added to transport batch ' . $batchNumber);
    }

    /**
     * Display the specified transport.
     */
    public function show(Transport $transport): View
    {
        return view('transports.show', compact('transport'));
    }

    /**
     * Show the form for editing the specified transport.
     */
    public function edit(Transport $transport): View
    {
        if (auth()->user()->hasRole('Transporter')) {
            abort(403, 'Unauthorized action.');
        }
        
    //    $this->authorize('edit transports');
        $vehicles = Vehicle::where('status', '!=', 'sold')
                          ->orderBy('stock_number')
                          ->get();
        $transporters = Transporter::where('is_active', true)
                                 ->orderBy('name')
                                 ->get();
        return view('transports.edit', compact('transport', 'vehicles', 'transporters'));
    }

    /**
     * Update the specified transport in storage.
     */
    public function update(Request $request, Transport $transport): RedirectResponse
    {
        if (auth()->user()->hasRole('Transporter')) {
            abort(403, 'Unauthorized action.');
        }
        
       // $this->authorize('edit transports');
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'transporter_id' => 'nullable|exists:transporters,id',
            'origin' => 'nullable|string|max:255',
            'destination' => 'required|string|max:255',
            'pickup_date' => 'nullable|date',
            'delivery_date' => 'nullable|date',
            'status' => 'required|string|in:pending,in_transit,delivered,cancelled',
            'transporter_name' => 'nullable|string|max:255',
            'transporter_phone' => 'nullable|string|max:255',
            'transporter_email' => 'nullable|string|email|max:255',
            'notes' => 'nullable|string',
            'gate_pass' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'additional_vehicle_ids' => 'nullable|array',
            'additional_vehicle_ids.*' => 'exists:vehicles,id',
            'remove_vehicle_ids' => 'nullable|array',
            'remove_vehicle_ids.*' => 'exists:transports,id',
            'generate_qr' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        
        try {
            // If transporter_id is selected, clear manual transporter fields
            if (!empty($validated['transporter_id'])) {
                $validated['transporter_name'] = null;
                $validated['transporter_phone'] = null;
                $validated['transporter_email'] = null;
            }

            // Update vehicle transport status
            $vehicle = Vehicle::findOrFail($request->vehicle_id);
            if ($request->status == 'in_transit') {
                $vehicle->update(['transport_status' => 'in_transit']);
            } elseif ($request->status == 'delivered') {
                $vehicle->update(['transport_status' => 'delivered']);
            } elseif ($request->status == 'cancelled') {
                $vehicle->update(['transport_status' => null]);
            }

            // Handle gate pass upload if provided
            if ($request->hasFile('gate_pass')) {
                // Delete old file if exists
                if ($transport->gate_pass_path) {
                    Storage::disk('public')->delete($transport->gate_pass_path);
                }
                
                $file = $request->file('gate_pass');
                $path = $file->store('gate-passes', 'public');
                $validated['gate_pass_path'] = $path;
            }

            // Handle QR code generation if requested
            if ($request->generate_qr) {
                // Delete old QR code if exists
                if ($transport->qr_code_path) {
                    Storage::disk('public')->delete($transport->qr_code_path);
                }
                
                // Create full absolute URL for the tracking page
                $qrUrl = url("/track/{$transport->batch_id}");
                $qrPath = 'qrcodes/' . $transport->batch_id . '.png';
                $qrCode = QrCode::format('png')->size(300)->generate($qrUrl);
                Storage::disk('public')->put($qrPath, $qrCode);
                $validated['qr_code_path'] = $qrPath;
            }

            // Update the transport record
            $transport->update($validated);
            
            // Handle batch management - add vehicles
            if ($request->has('additional_vehicle_ids') && is_array($request->additional_vehicle_ids)) {
                // Get transport data for new vehicles to reuse
                $transportData = [
                    'batch_id' => $transport->batch_id,
                    'batch_name' => $transport->batch_name,
                    'origin' => $transport->origin,
                    'destination' => $transport->destination,
                    'pickup_date' => $transport->pickup_date,
                    'delivery_date' => $transport->delivery_date,
                    'status' => $transport->status,
                    'transporter_id' => $transport->transporter_id,
                    'transporter_name' => $transport->transporter_name,
                    'transporter_phone' => $transport->transporter_phone,
                    'transporter_email' => $transport->transporter_email,
                    'notes' => $transport->notes,
                    'qr_code_path' => $transport->qr_code_path,
                ];
                
                foreach ($request->additional_vehicle_ids as $vehicleId) {
                    $newVehicle = Vehicle::findOrFail($vehicleId);
                    
                    // Create new transport record for this vehicle in the same batch
                    $newTransport = new Transport($transportData);
                    $newTransport->vehicle_id = $vehicleId;
                    $newTransport->save();
                    
                    // Update vehicle status if needed
                    if ($transport->status == 'in_transit') {
                        $newVehicle->update(['transport_status' => 'in_transit']);
                    }
                }
            }
            
            // Handle batch management - remove vehicles
            if ($request->has('remove_vehicle_ids') && is_array($request->remove_vehicle_ids)) {
                foreach ($request->remove_vehicle_ids as $transportId) {
                    $transportToRemove = Transport::find($transportId);
                    
                    if ($transportToRemove && $transportToRemove->batch_id === $transport->batch_id) {
                        // Update vehicle status
                        if ($transportToRemove->vehicle) {
                            $transportToRemove->vehicle->update(['transport_status' => null]);
                        }
                        
                        // Delete gate pass file if exists
                        if ($transportToRemove->gate_pass_path) {
                            Storage::disk('public')->delete($transportToRemove->gate_pass_path);
                        }
                        
                        // Delete transport record
                        $transportToRemove->delete();
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('transports.index')
                             ->with('success', 'Transport updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update transport: ' . $e->getMessage()]);
        }
    }

    /**
     * Display transports by batch ID.
     */
    public function showBatch(string $batchId): View
    {
        $transports = Transport::where('batch_id', $batchId)
                              ->with('vehicle')
                              ->get();
                              
        if ($transports->isEmpty()) {
            abort(404, 'Batch not found');
        }
        
        // Use the first transport to get common batch data
        $batchData = $transports->first();
        
        return view('transports.batch', compact('transports', 'batchData', 'batchId'));
    }

    /**
     * Track batch via QR code - public access without header/footer.
     */
    public function trackBatch(string $batchId): View
    {
        $transports = Transport::where('batch_id', $batchId)
                              ->with('vehicle')
                              ->get();
                              
        if ($transports->isEmpty()) {
            abort(404, 'Batch not found');
        }
        
        // Use the first transport to get common batch data
        $batchData = $transports->first();
        
        return view('transports.track', compact('transports', 'batchData', 'batchId'));
    }

    /**
     * Remove the specified transport from storage.
     */
    public function destroy(Transport $transport): RedirectResponse
    {
        // Check if user has admin or manager role
        if (!auth()->user()->hasAnyRole(['Admin', 'Manager'])) {
            abort(403, 'Unauthorized action.');
        }

        DB::beginTransaction();

        try {
            // Reset the vehicle's transport status
            if ($transport->vehicle) {
                $transport->vehicle->update(['transport_status' => null]);
            }

            // Delete any associated gate pass files
            if ($transport->gate_pass_path) {
                Storage::disk('public')->delete($transport->gate_pass_path);
            }

            // Delete the transport record
            $transport->delete();

            // Check if this was part of a batch and update batch status if needed
            if ($transport->batch_id) {
                $remainingTransports = Transport::where('batch_id', $transport->batch_id)->count();
                
                if ($remainingTransports === 0) {
                    // If no transports left, delete the batch
                    Batch::where('id', $transport->batch_id)->delete();
                }
            }

            DB::commit();

            return redirect()->back()
                ->with('success', 'Transport deleted successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error deleting transport: ' . $e->getMessage());
        }
    }

    /**
     * Acknowledge a transport.
     */
    public function acknowledge(Transport $transport): RedirectResponse
    {
        // Check if the user is authorized to acknowledge transports
        if (!auth()->user()->hasRole('Transporter')) {
            return redirect()->back()->with('error', 'You are not authorized to acknowledge transports.');
        }

      
        // Check if the transport belongs to the user's transporter
        if ($transport->transporter_id !== auth()->user()->transporter_id) {
            return redirect()->back()->with('error', 'You can only acknowledge transports assigned to your company.');
        }
     
        // Check if already acknowledged
        if ($transport->is_acknowledged) {
            return redirect()->back()->with('info', 'This transport has already been acknowledged.');
        }

     
        DB::beginTransaction();
        
        try {
            $transport->update([
                'is_acknowledged' => true,
                'acknowledged_at' => now(),
                'acknowledged_by' => auth()->id(),
            ]);

            // Update vehicle status if needed
            if ($transport->status === 'pending') {
                $transport->update(['status' => 'in_transit']);
                $transport->vehicle->update(['transport_status' => 'in_transit']);
            }

            DB::commit();
            
            return redirect()->back()->with('success', 'Transport acknowledged successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to acknowledge transport: ' . $e->getMessage());
        }
    }

    /**
     * Update transport status.
     */
    public function updateTransportStatus(Request $request, Transport $transport): RedirectResponse
    {

        // Verify user is a transporter and has access to this transport
        if (!auth()->user()->hasRole('Transporter') || 
            auth()->user()->transporter_id !== $transport->transporter_id) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'status' => 'required|string|in:in_transit,delivered',
            'pickup_date' => 'nullable|date',
            'delivery_date' => 'nullable|date',
        ]);

        DB::beginTransaction();

        try {
            // Prepare transport update data
            $transportUpdateData = ['status' => $validated['status']];
            
            // Add dates based on status
            if ($validated['status'] === 'in_transit' && isset($validated['pickup_date'])) {
                $transportUpdateData['pickup_date'] = $validated['pickup_date'];
            }
            if ($validated['status'] === 'delivered' && isset($validated['delivery_date'])) {
                $transportUpdateData['delivery_date'] = $validated['delivery_date'];
            }

            // Update transport status and dates
            $transport->update($transportUpdateData);

            // Update vehicle status
            if ($transport->vehicle) {
                $transport->vehicle->update([
                    'transport_status' => $validated['status']
                ]);

                
            }

            DB::commit();

            $statusMessage = ucfirst(str_replace('_', ' ', $validated['status']));
            return redirect()->back()
                ->with('success', "Transport successfully marked as {$statusMessage}.");

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating transport status: ' . $e->getMessage());
        }
    }

    /**
     * Update transport status for multiple vehicles in a batch.
     */
    public function updateBatchStatus(Request $request, string $batchId): RedirectResponse
    {
    
        // Verify user is a transporter
        if (!auth()->user()->hasRole('Transporter')) {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'transport_ids' => 'required|array',
            'transport_ids.*' => 'exists:transports,id',
            'status' => 'required|string|in:picked_up,delivered',
            'pickup_date' => 'nullable|date',
            'delivery_date' => 'nullable|date',
        ]);

        DB::beginTransaction();

        try {
            // Get all transports in this batch that belong to the authenticated transporter
            $transports = Transport::whereIn('id', $validated['transport_ids'])
                ->where('batch_id', $batchId)
                ->where('transporter_id', auth()->user()->transporter_id)
                ->get();
           
            if ($transports->isEmpty()) {
                return redirect()->back()->with('error', 'No valid transports found to update.');
            }

            // Get the batch record
            $batch = Transport::where('batch_id', $batchId)->first();
            
            if (!$batch) {
                return redirect()->back()->with('error', 'Batch not found.');
            }
           
            // Prepare update data for transports
            $transportUpdateData = ['status' => $validated['status']];
            if ($validated['status'] === 'in_transit' && isset($validated['pickup_date'])) {
                $transportUpdateData['pickup_date'] = $validated['pickup_date'];
            }
            if ($validated['status'] === 'delivered' && isset($validated['delivery_date'])) {
                $transportUpdateData['delivery_date'] = $validated['delivery_date'];
            }

            // Update each selected transport and its associated vehicle
            foreach ($transports as $transport) {
                // Update transport status and dates
                $transport->update($transportUpdateData);
                
                // Update vehicle transport status and status
                if ($transport->vehicle) {
                    $vehicleUpdateData = [
                        'transport_status' => $validated['status'],
                        'status' => $validated['status'] // Update vehicle status to match transport status
                    ];
                    
                    $transport->vehicle->update($vehicleUpdateData);
                }
            }

            // Check if all vehicles in the batch should update the batch status
            $allTransportsInBatch = Transport::where('batch_id', $batchId)->get();
            $selectedTransportIds = collect($validated['transport_ids']);

            // Count statuses for all transports in the batch
            $statusCounts = $allTransportsInBatch->groupBy('status')->map->count();
            
            // Determine if batch status should be updated
            $shouldUpdateBatchStatus = false;
            
            if ($validated['status'] === 'in_transit') {
                // Update batch to in_transit if any transport is in_transit
                $shouldUpdateBatchStatus = true;
                $batchStatus = 'in_transit';
            } elseif ($validated['status'] === 'delivered') {
                // Only update batch to delivered if ALL transports are delivered
                $allDelivered = $allTransportsInBatch->every(function ($transport) {
                    return $transport->status === 'delivered';
                });
                
                if ($allDelivered) {
                    $shouldUpdateBatchStatus = true;
                    $batchStatus = 'delivered';
                }
            }

            // Update batch status if needed
            if ($shouldUpdateBatchStatus) {
                $batchUpdateData = ['status' => $batchStatus];
                
                if ($batchStatus === 'in_transit' && isset($validated['pickup_date'])) {
                    $batchUpdateData['pickup_date'] = $validated['pickup_date'];
                }
                if ($batchStatus === 'delivered' && isset($validated['delivery_date'])) {
                    $batchUpdateData['delivery_date'] = $validated['delivery_date'];
                }
                
                $batch->update($batchUpdateData);
            }
            
            DB::commit();
            
            $message = 'Transport status updated successfully for ' . $transports->count() . ' vehicles.';
            if ($shouldUpdateBatchStatus) {
                $message .= ' Batch status updated to ' . ucfirst($batchStatus) . '.';
            }
            
            return redirect()->back()->with('success', $message);
                
        } catch (\Exception $e) {
         
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error updating transport status: ' . $e->getMessage());
        }
    }

    /**
     * Acknowledge all transports in a batch.
     */
    public function acknowledgeBatch(string $batchId): RedirectResponse
    {
        // Check if the user is authorized to acknowledge transports
        if (!auth()->user()->hasRole('Transporter')) {
            return redirect()->back()->with('error', 'You are not authorized to acknowledge transports.');
        }

        DB::beginTransaction();
        
        try {
            // Get all unacknowledged transports in this batch for the current transporter
            $transports = Transport::where('batch_id', $batchId)
                ->where('transporter_id', auth()->user()->transporter_id)
                ->where('is_acknowledged', false)
                ->get();

            if ($transports->isEmpty()) {
                return redirect()->back()->with('info', 'No transports to acknowledge in this batch.');
            }

            foreach ($transports as $transport) {
                $transport->update([
                    'is_acknowledged' => true,
                    'acknowledged_at' => now(),
                    'acknowledged_by' => auth()->id(),
                ]);

                // Update vehicle status if needed
                if ($transport->status === 'pending') {
                    $transport->update(['status' => 'in_transit']);
                    if ($transport->vehicle) {
                        $transport->vehicle->update(['transport_status' => 'in_transit']);
                    }
                }
            }

            DB::commit();
            
            return redirect()->back()->with('success', 'All transports in batch acknowledged successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to acknowledge transports: ' . $e->getMessage());
        }
    }
} 