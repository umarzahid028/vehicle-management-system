<?php

namespace App\Http\Controllers;

use App\Models\VendorType;
use Illuminate\Http\Request;

class VendorTypeController extends Controller
{
    /**
     * Display a listing of the vendor types.
     */
    public function index()
    {
        $vendorTypes = VendorType::orderBy('name')->get();
        return view('vendor-types.index', compact('vendorTypes'));
    }
    
    /**
     * Show the form for creating a new vendor type.
     */
    public function create()
    {
        return view('vendor-types.create');
    }
    
    /**
     * Store a newly created vendor type in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:vendor_types',
            'description' => 'nullable|string',
            'is_on_site' => 'boolean',
            'has_system_access' => 'boolean',
            'is_active' => 'boolean',
        ]);
        
        // Set defaults for checkbox fields if not provided
        $validated['is_on_site'] = $validated['is_on_site'] ?? false;
        $validated['has_system_access'] = $validated['has_system_access'] ?? false;
        $validated['is_active'] = $validated['is_active'] ?? true;
        
        VendorType::create($validated);
        
        return redirect()->route('vendor-types.index')
                         ->with('success', 'Vendor type created successfully');
    }
    
    /**
     * Show the form for editing the specified vendor type.
     */
    public function edit(VendorType $vendorType)
    {
        return view('vendor-types.edit', compact('vendorType'));
    }
    
    /**
     * Update the specified vendor type in storage.
     */
    public function update(Request $request, VendorType $vendorType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:vendor_types,name,' . $vendorType->id,
            'description' => 'nullable|string',
            'is_on_site' => 'boolean',
            'has_system_access' => 'boolean',
            'is_active' => 'boolean',
        ]);
        
        // Set defaults for checkbox fields if not provided
        $validated['is_on_site'] = $validated['is_on_site'] ?? false;
        $validated['has_system_access'] = $validated['has_system_access'] ?? false;
        $validated['is_active'] = $validated['is_active'] ?? false;
        
        $vendorType->update($validated);
        
        return redirect()->route('vendor-types.index')
                         ->with('success', 'Vendor type updated successfully');
    }
    
    /**
     * Remove the specified vendor type from storage.
     */
    public function destroy(VendorType $vendorType)
    {
        // Check if vendor type has any vendors associated
        if ($vendorType->vendors()->count() > 0) {
            return redirect()->route('vendor-types.index')
                             ->with('error', 'Cannot delete vendor type with associated vendors');
        }
        
        $vendorType->delete();
        
        return redirect()->route('vendor-types.index')
                         ->with('success', 'Vendor type deleted successfully');
    }
    
    /**
     * Toggle the active status of the vendor type.
     */
    public function toggleActive(VendorType $vendorType)
    {
        $vendorType->update(['is_active' => !$vendorType->is_active]);
        
        return redirect()->route('vendor-types.index')
                         ->with('success', 'Vendor type status updated successfully');
    }
}
