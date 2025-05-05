<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\VendorType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Notifications\LoginCredentials;
use Illuminate\Support\Facades\DB;
use App\Enums\Role;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    protected $specialties = [
        'mechanical' => 'Mechanical',
        'body_shop' => 'Body Shop',
        'detail' => 'Detailing',
        'tire' => 'Tire Services',
        'upholstery' => 'Upholstery',
        'glass' => 'Glass Repair',
        'other' => 'Other Services',
    ];

    /**
     * Display a listing of the vendors.
     */
    public function index(Request $request)
    {
        $type = $request->query('type');
        $query = Vendor::query()->with('type');
        
        if ($type) {
            $query->where('specialty_tags', 'like', '%' . $type . '%');
        }
        
        $vendors = $query->orderBy('created_at', 'desc')->get();
        
        return view('vendors.index', [
            'vendors' => $vendors,
            'specialties' => $this->specialties,
            'type' => $type,
            'types' => $this->specialties, // Use specialties for the filter dropdown
        ]);
    }

    /**
     * Show the form for creating a new vendor.
     */
    public function create()
    {
        $types = VendorType::where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('vendors.create', [
            'specialties' => $this->specialties,
            'types' => $types,
        ]);
    }

    /**
     * Store a newly created vendor in storage.
     */
    public function store(Request $request)
    {
        // Get vendor type to determine if password is required
        $vendorType = null;
        $isOffSite = false;
        $hasSystemAccess = false;
        
        if (!empty($request->input('type_id'))) {
            $vendorType = VendorType::find($request->input('type_id'));
            $isOffSite = $vendorType && !$vendorType->is_on_site;
            $hasSystemAccess = $vendorType && $vendorType->has_system_access;
        }
        
        // Define base validation rules
        $validationRules = [
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'specialty_tags' => 'required|array|min:1',
            'specialty_tags.*' => Rule::in(array_keys($this->specialties)),
            'type_id' => 'nullable|exists:vendor_types,id',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
        
        // Only require password for vendors with system access
        if ($hasSystemAccess) {
            $validationRules['password'] = 'required|string|min:8|confirmed';
        }
        
        $validated = $request->validate($validationRules);
        
        // Set is_active default if not provided
        $validated['is_active'] = $validated['is_active'] ?? true;
        
        // Store the password before creating the vendor
        $password = $request->input('password');
        
        DB::beginTransaction();
        
        try {
            // Create the vendor
            $vendor = Vendor::create($validated);

            // Get vendor type
            $vendorType = null;
            if (!empty($validated['type_id'])) {
                $vendorType = VendorType::find($validated['type_id']);
            }

            // Only create or update user account if vendor has system access
            if ($vendorType && $vendorType->has_system_access) {
                // Check if user already exists
                $user = User::where('email', $validated['email'])->first();

                if ($user) {
                    // If user exists, ensure they have the Vendor role
                    $user->assignRole('Vendor');
                    
                    // Set vendor role based on vendor type
                    $user->update([
                        'name' => $validated['contact_person'] ?? $validated['name'],
                        'role' => $vendorType && $vendorType->is_on_site ? 
                            Role::ONSITE_VENDOR : 
                            Role::OFFSITE_VENDOR,
                        'is_active' => $validated['is_active'] && ($vendorType ? $vendorType->has_system_access : false),
                    ]);
                } else {
                    // Create new user account
                    $user = User::create([
                        'name' => $validated['contact_person'] ?? $validated['name'],
                        'email' => $validated['email'],
                        'password' => Hash::make($password),
                        'role' => $vendorType && $vendorType->is_on_site ? 
                            Role::ONSITE_VENDOR : 
                            Role::OFFSITE_VENDOR,
                        'is_active' => $validated['is_active'] && ($vendorType ? $vendorType->has_system_access : false),
                    ]);

                    // Assign vendor role
                    $user->assignRole('Vendor');

                    // Send welcome notification
                    $user->notify(new LoginCredentials('(your chosen password)', 'Vendor'));
                }
            }
            
            DB::commit();
            
            return redirect()->route('vendors.index')
                ->with('success', 'Vendor created successfully.');
                
        } catch (\Illuminate\Database\QueryException $e) {
            DB::rollBack();
            
            // Check specifically for duplicate entry error
            if ($e->errorInfo[1] == 1062) {
                return redirect()->back()->withInput()
                    ->with('error', 'A user with this email already exists.');
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the vendor: ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the vendor: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified vendor.
     */
    public function show(Vendor $vendor)
    {
        $vendor->load('type');
        
        $inspectionItems = $vendor->inspectionItemResults()
            ->with(['inspectionItem', 'vehicleInspection.vehicle'])
            ->get()
            ->groupBy('vehicleInspection.vehicle_id');
            
        return view('vendors.show', [
            'vendor' => $vendor,
            'inspectionItems' => $inspectionItems,
            'types' => VendorType::orderBy('name')->get(),
            'specialties' => $this->specialties,
        ]);
    }

    /**
     * Show the form for editing the specified vendor.
     */
    public function edit(Vendor $vendor)
    {
        $types = VendorType::orderBy('name')->get();
        
        return view('vendors.edit', [
            'vendor' => $vendor,
            'specialties' => $this->specialties,
            'types' => $types,
        ]);
    }

    /**
     * Update the specified vendor in storage.
     */
    public function update(Request $request, Vendor $vendor)
    {
        // Get vendor type to determine if password is required
        $vendorType = null;
        $isOffSite = false;
        $hasSystemAccess = false;
        
        if (!empty($request->input('type_id'))) {
            $vendorType = VendorType::find($request->input('type_id'));
            $isOffSite = $vendorType && !$vendorType->is_on_site;
            $hasSystemAccess = $vendorType && $vendorType->has_system_access;
        }
        
        // Define base validation rules
        $validationRules = [
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                $vendor->user ? Rule::unique('users')->ignore($vendor->user->id) : 'unique:users',
            ],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'specialty_tags' => 'required|array|min:1',
            'specialty_tags.*' => Rule::in(array_keys($this->specialties)),
            'type_id' => 'nullable|exists:vendor_types,id',
            'notes' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
        
        // Only require password validation if provided or if this is a new user with system access
        if ($request->filled('password') && $hasSystemAccess) {
            $validationRules['password'] = 'string|min:8|confirmed';
        }
        
        $validated = $request->validate($validationRules);
        
        // Set is_active default if not provided
        $validated['is_active'] = $validated['is_active'] ?? false;
        
        // Start transaction
        DB::beginTransaction();
        
        try {
            // Update vendor
            $vendor->update($validated);
            
            // Get vendor type
            $vendorType = null;
            if (!empty($validated['type_id'])) {
                $vendorType = VendorType::find($validated['type_id']);
            }
            
            // Only create or update user account if vendor has system access
            if ($vendorType && $vendorType->has_system_access) {
                // Update associated user account
                $userUpdate = [
                    'name' => $validated['contact_person'] ?? $validated['name'],
                    'email' => $validated['email'],
                    'role' => $vendorType && $vendorType->is_on_site ? 
                        Role::ONSITE_VENDOR : 
                        Role::OFFSITE_VENDOR,
                    'is_active' => $validated['is_active'] && ($vendorType ? $vendorType->has_system_access : false),
                ];
                
                // Only update password if provided
                if ($request->filled('password')) {
                    $userUpdate['password'] = Hash::make($request->input('password'));
                }
                
                // Update or create the user
                if ($vendor->user) {
                    $vendor->user()->update($userUpdate);
                } else {
                    // Create new user if doesn't exist but now has system access
                    $userUpdate['password'] = Hash::make($request->input('password') ?? Str::random(12));
                    $user = User::create($userUpdate);
                    $user->assignRole('Vendor');
                }
            } else if ($vendor->user) {
                // If vendor no longer has system access but has a user account, deactivate it
                $vendor->user()->update([
                    'is_active' => false
                ]);
            }
            
            DB::commit();
            
            return redirect()->route('vendors.index')
                ->with('success', 'Vendor updated successfully.');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while updating the vendor: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified vendor from storage.
     */
    public function destroy(Vendor $vendor)
    {
        // Check if the vendor has any associated inspections or items
        if ($vendor->vehicleInspections()->count() > 0 || $vendor->inspectionItemResults()->count() > 0) {
            return redirect()->route('vendors.index')
                ->with('error', 'Cannot delete a vendor with associated inspections or repair items.');
        }
        
        $vendor->delete();
        
        return redirect()->route('vendors.index')
            ->with('success', 'Vendor deleted successfully.');
    }

    /**
     * Toggle active status of the vendor.
     */
    public function toggleActive(Vendor $vendor)
    {
        $vendor->update(['is_active' => !$vendor->is_active]);
        
        return redirect()->route('vendors.index')
            ->with('success', 'Vendor status updated successfully.');
    }
} 