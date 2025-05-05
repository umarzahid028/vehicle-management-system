<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\GatePass;
use App\Models\Vehicle;
use App\Models\Transporter;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GatePassController extends Controller
{
    /**
     * Display a listing of gate passes.
     */
    public function index(Request $request): View
    {
        $query = GatePass::with(['vehicle', 'transporter', 'batch'])->latest();

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('pass_number', 'like', "%{$search}%")
                  ->orWhereHas('vehicle', function($q2) use ($search) {
                      $q2->where('stock_number', 'like', "%{$search}%")
                          ->orWhere('vin', 'like', "%{$search}%");
                  })
                  ->orWhereHas('transporter', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('batch', function($q2) use ($search) {
                      $q2->where('batch_number', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $gatePasses = $query->paginate(15);
        return view('gate-passes.index', compact('gatePasses'));
    }

    /**
     * Show the form for creating a new gate pass.
     */
    public function create(): View
    {
        $vehicles = Vehicle::whereDoesntHave('gatePasses', function($query) {
                              $query->whereIn('status', ['pending', 'approved']);
                           })
                           ->where('status', '!=', 'sold')
                           ->orderBy('stock_number')
                           ->get();
                           
        $transporters = Transporter::where('is_active', true)
                                  ->orderBy('name')
                                  ->get();
                                  
        $batches = Batch::whereIn('status', ['pending', 'in_transit'])
                        ->orderBy('created_at', 'desc')
                        ->get();
                        
        return view('gate-passes.create', compact('vehicles', 'transporters', 'batches'));
    }

    /**
     * Store a newly created gate pass in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'transporter_id' => 'required|exists:transporters,id',
            'batch_id' => 'nullable|exists:batches,id',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'status' => 'required|string|in:pending,approved,used,rejected,expired',
            'notes' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Generate a unique pass number
        $passNumber = 'GP-' . date('ym') . '-' . strtoupper(Str::random(5));
        
        // Handle file upload
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('gate-passes', 'public');
        }
        
        // Create the gate pass
        $gatePass = GatePass::create([
            'pass_number' => $passNumber,
            'vehicle_id' => $validated['vehicle_id'],
            'transporter_id' => $validated['transporter_id'],
            'batch_id' => $validated['batch_id'],
            'status' => $validated['status'],
            'issue_date' => $validated['issue_date'],
            'expiry_date' => $validated['expiry_date'],
            'authorized_by' => Auth::id(),
            'file_path' => $filePath,
            'notes' => $validated['notes'],
        ]);

        return redirect()->route('gate-passes.show', $gatePass)
                         ->with('success', 'Gate pass created successfully.');
    }

    /**
     * Display the specified gate pass.
     */
    public function show(GatePass $gatePass): View
    {
        $gatePass->load(['vehicle', 'transporter', 'batch']);
        return view('gate-passes.show', compact('gatePass'));
    }

    /**
     * Show the form for editing the specified gate pass.
     */
    public function edit(GatePass $gatePass): View
    {
        $vehicles = Vehicle::where(function($query) use ($gatePass) {
                              $query->whereDoesntHave('gatePasses', function($q) {
                                  $q->whereIn('status', ['pending', 'approved']);
                              })
                              ->orWhere('id', $gatePass->vehicle_id);
                           })
                           ->where('status', '!=', 'sold')
                           ->orderBy('stock_number')
                           ->get();
                           
        $transporters = Transporter::where('is_active', true)
                                  ->orderBy('name')
                                  ->get();
                                  
        $batches = Batch::whereIn('status', ['pending', 'in_transit'])
                        ->orderBy('created_at', 'desc')
                        ->get();
                        
        return view('gate-passes.edit', compact('gatePass', 'vehicles', 'transporters', 'batches'));
    }

    /**
     * Update the specified gate pass in storage.
     */
    public function update(Request $request, GatePass $gatePass): RedirectResponse
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'transporter_id' => 'required|exists:transporters,id',
            'batch_id' => 'nullable|exists:batches,id',
            'issue_date' => 'required|date',
            'expiry_date' => 'required|date|after:issue_date',
            'status' => 'required|string|in:pending,approved,used,rejected,expired',
            'notes' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        // Handle file upload
        $filePath = $gatePass->file_path;
        if ($request->hasFile('file')) {
            // Delete old file if exists
            if ($gatePass->file_path) {
                Storage::disk('public')->delete($gatePass->file_path);
            }
            $filePath = $request->file('file')->store('gate-passes', 'public');
        }
        
        // Update the gate pass
        $gatePass->update([
            'vehicle_id' => $validated['vehicle_id'],
            'transporter_id' => $validated['transporter_id'],
            'batch_id' => $validated['batch_id'],
            'status' => $validated['status'],
            'issue_date' => $validated['issue_date'],
            'expiry_date' => $validated['expiry_date'],
            'file_path' => $filePath,
            'notes' => $validated['notes'],
        ]);
        
        // Add used_at timestamp if status is 'used'
        if ($validated['status'] === 'used' && !$gatePass->used_at) {
            $gatePass->update(['used_at' => now()]);
        }

        return redirect()->route('gate-passes.show', $gatePass)
                         ->with('success', 'Gate pass updated successfully.');
    }

    /**
     * Update the gate pass status only.
     */
    public function updateStatus(Request $request, GatePass $gatePass): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:pending,approved,used,rejected,expired',
        ]);
        
        $data = ['status' => $validated['status']];
        
        // Add used_at timestamp if status is 'used'
        if ($validated['status'] === 'used' && !$gatePass->used_at) {
            $data['used_at'] = now();
        }
        
        $gatePass->update($data);

        return redirect()->route('gate-passes.show', $gatePass)
                         ->with('success', 'Gate pass status updated successfully.');
    }

    /**
     * Remove the specified gate pass from storage.
     */
    public function destroy(GatePass $gatePass): RedirectResponse
    {
        // Delete file if exists
        if ($gatePass->file_path) {
            Storage::disk('public')->delete($gatePass->file_path);
        }
        
        $gatePass->delete();

        return redirect()->route('gate-passes.index')
                         ->with('success', 'Gate pass removed successfully.');
    }
    
    /**
     * Download the gate pass file.
     */
    public function download(GatePass $gatePass)
    {
        if (!$gatePass->file_path) {
            return redirect()->back()->with('error', 'No file attached to this gate pass.');
        }
        
        return Storage::disk('public')->download($gatePass->file_path, $gatePass->pass_number . '.pdf');
    }
} 