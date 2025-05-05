<?php

namespace App\Http\Controllers;

use App\Models\GoodwillClaim;
use App\Models\Vehicle;
use App\Models\SalesIssue;
use App\Models\User;
use App\Notifications\NewGoodwillClaimSubmitted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class GoodwillClaimController extends Controller
{
    /**
     * Create a new controller instance.
     */
    

    /**
     * Display a listing of goodwill claims.
     */
    public function index(Request $request)
    {
        $query = GoodwillClaim::with(['vehicle', 'salesIssue', 'createdBy', 'approvedBy']);

        // Filter by status
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // Filter by vehicle
        if ($vehicleId = $request->query('vehicle_id')) {
            $query->where('vehicle_id', $vehicleId);
        }

        $claims = $query->latest()->paginate(10);

        return view('sales.goodwill-claims.index', compact('claims'));
    }

    /**
     * Show the form for creating a new goodwill claim.
     */
    public function create(Request $request)
    {
        
        $vehicle = null;
        $salesIssue = null;

        if ($vehicleId = $request->query('vehicle_id')) {
            $vehicle = Vehicle::findOrFail($vehicleId);
        }

        if ($issueId = $request->query('sales_issue_id')) {
            $salesIssue = SalesIssue::with('vehicle')->findOrFail($issueId);
            $vehicle = $salesIssue->vehicle;
        }

        // Get all active vehicles for the dropdown
        $vehicles = Vehicle::orderBy('stock_number')
            ->select('id', 'stock_number', 'year', 'make', 'model')
            ->where('status', 'Sold')
            ->get();
           

        return view('sales.goodwill-claims.create', [
            'vehicles' => $vehicles,
            'selectedVehicle' => $vehicle,
            'salesIssue' => $salesIssue,
        ]);
    }

    /**
     * Store a newly created goodwill claim in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'sales_issue_id' => 'nullable|exists:sales_issues,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'issue_description' => 'required|string',
            'requested_resolution' => 'required|string',
            'customer_consent' => 'required|boolean',
            'estimated_cost' => 'nullable|numeric|min:0',
        ]);

        $validated['created_by_user_id'] = Auth::id();
        $validated['status'] = 'pending';
        
        if ($validated['customer_consent']) {
            $validated['customer_consent_date'] = now();
        }

        $claim = GoodwillClaim::create($validated);

        // Notify admins and managers
        $managers = User::role(['Admin', 'Sales Manager'])->get();
        Notification::send($managers, new NewGoodwillClaimSubmitted($claim));

        //goodwill-claims/{claim}/signature
        return redirect()->route('sales.goodwill-claims.show',  $claim->id.'/signature')
            ->with('success', 'Goodwill claim submitted successfully.');
    }

    /**
     * Display the specified goodwill claim.
     */
    public function show($id)
    {
        $claim = GoodwillClaim::findOrFail($id);
        $claim->load(['vehicle', 'salesIssue', 'createdBy', 'approvedBy']);

        return view('sales.goodwill-claims.show', compact('claim'));
    }

    /**
     * Update the specified goodwill claim status.
     */
    public function updateStatus(Request $request, GoodwillClaim $claim)
    {
     
        $validated = $request->validate([
            'status' => 'required|in:pending,approved,rejected',
            'estimated_cost' => 'required_if:status,approved|nullable|numeric|min:0',
        ]);
        

        $claim->update([
            'status' => $validated['status'],
            'estimated_cost' => $validated['estimated_cost'],
            'approved_by_user_id' => Auth::id(),
            'approved_at' => in_array($validated['status'], ['approved', 'rejected']) ? now() : null,
        ]);

        return redirect()->route('sales.goodwill-claims.show', ['goodwill_claim' => $claim])
            ->with('success', 'Claim status updated successfully.');
    }

    /**
     * Update customer consent for the goodwill claim.
     */
    public function updateConsent(Request $request, GoodwillClaim $claim)
    {
        $validated = $request->validate([
            'customer_consent' => 'required|boolean',
        ]);

        $claim->update([
            'customer_consent' => $validated['customer_consent'],
            'customer_consent_date' => $validated['customer_consent'] ? now() : null,
        ]);

        return redirect()->route('sales.goodwill-claims.show', ['goodwill_claim' => $claim])
            ->with('success', 'Customer consent updated successfully.');
    }

    /**
     * Show the signature capture form.
     */
    public function showSignatureForm(GoodwillClaim $claim)
    {
        $claim->load(['vehicle', 'salesIssue', 'createdBy']);
        return view('sales.goodwill-claims.capture-signature', compact('claim'));
    }

    /**
     * Store the customer signature for the goodwill claim.
     */
    public function storeSignature(Request $request, GoodwillClaim $claim)
    {
        $validated = $request->validate([
            'signature' => 'required|string',
        ]);

        // Remove data:image/png;base64, part if it exists
        $signature = $validated['signature'];
        if (strpos($signature, 'data:image/png;base64,') === 0) {
            $signature = substr($signature, 22);
        }

        $claim->update([
            'customer_signature' => $signature,
            'signed_in_person' => true,
            'signature_date' => now(),
            'customer_consent' => true,
            'customer_consent_date' => now(),
        ]);

        return redirect()->route('sales.goodwill-claims.show', ['goodwill_claim' => $claim])
            ->with('success', 'Customer signature captured successfully.');
    }
} 