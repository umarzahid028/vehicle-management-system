<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\User;
use App\Notifications\NewVehicleArrival;
use App\Events\NewVehicleEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class VehicleController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        
        // Allow transporters to only access index and show methods
        $this->middleware(function ($request, $next) {
            if (auth()->user()->hasRole('Transporter')) {
                if (!in_array($request->route()->getActionMethod(), ['index', 'show'])) {
                    abort(403, 'Unauthorized action.');
                }
            }
            return $next($request);
        });
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vehicles = Vehicle::query();
        
        // Apply search filters
        if ($request->filled('search')) {
            $search = $request->input('search');
            $vehicles->where(function($query) use ($search) {
                $query->where('stock_number', 'like', "%{$search}%")
                    ->orWhere('vin', 'like', "%{$search}%")
                    ->orWhere('make', 'like', "%{$search}%")
                    ->orWhere('model', 'like', "%{$search}%");
            });
        }
        
        // Filter for unread vehicles
        if ($request->has('unread') && $request->input('unread') === 'true') {
            $vehicles->whereDoesntHave('vehicleReads', function($query) {
                $query->where('user_id', auth()->id());
            });
        }
        
        // Apply status filter
        if ($request->filled('status')) {
            $vehicles->where('status', $request->input('status'));
        }
        
        // Apply category filter
        if ($request->filled('category')) {
            $category = $request->input('category');
            $vehicles->byStatusCategory($category);
        }
        
        // Filter by specific common statuses
        if ($request->has('filter')) {
            $filter = $request->input('filter');
            
            switch ($filter) {
                case 'available':
                    $vehicles->available();
                    break;
                case 'transport':
                    $vehicles->inTransportProcess();
                    break;
                case 'inspection':
                    $vehicles->inInspectionProcess();
                    break;
                case 'repair':
                    $vehicles->inRepairProcess();
                    break;
                case 'sales':
                    $vehicles->inSalesProcess();
                    break;
                case 'sold':
                    $vehicles->sold();
                    break;
                case 'goodwill':
                    $vehicles->inGoodwillClaimsProcess();
                    break;
                case 'archive':
                    $vehicles->archived();
                    break;
            }
        }
        
        // Note: The global scope in Vehicle model will automatically filter for transporters
        $vehicles = $vehicles->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        // Get all status values for filter dropdown
        $statusOptions = app(\App\Services\VehicleStatusService::class)->getAllStatuses();
        $categoryOptions = [
            Vehicle::CATEGORY_AVAILABLE => 'Available',
            Vehicle::CATEGORY_TRANSPORT => 'Transport',
            Vehicle::CATEGORY_INSPECTION => 'Inspection',
            Vehicle::CATEGORY_REPAIR => 'Repair',
            Vehicle::CATEGORY_SALES => 'Sales',
            Vehicle::CATEGORY_GOODWILL => 'Goodwill Claims',
            Vehicle::CATEGORY_ARCHIVE => 'Archive',
        ];
        
        // Get the newly created vehicle ID from session (if exists)
        $newVehicleId = session('new_vehicle_id');
        
        // Get the updated vehicle ID from session (if exists)
        $updatedVehicleId = session('updated_vehicle_id');
        
        return view('vehicles.index', compact('vehicles', 'statusOptions', 'categoryOptions', 'newVehicleId', 'updatedVehicleId'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create vehicles');
        return view('vehicles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create vehicles');
        
        $validated = $request->validate([
            'stock_number' => 'required|unique:vehicles',
            'vin' => 'required|unique:vehicles',
            'year' => 'nullable|integer',
            'make' => 'nullable|string',
            'model' => 'nullable|string',
            'trim' => 'nullable|string',
            'date_in_stock' => 'nullable|date',
            'odometer' => 'nullable|integer',
            'exterior_color' => 'nullable|string',
            'interior_color' => 'nullable|string',
            'transmission' => 'nullable|string',
            'body_type' => 'nullable|string',
            'drive_train' => 'nullable|string',
            'engine' => 'nullable|string',
            'fuel_type' => 'nullable|string',
            'status' => 'nullable|string',
            'advertising_price' => 'nullable|numeric',
            'vehicle_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
        
        DB::beginTransaction();
        
        try {
            $vehicleData = $validated;
            
            // Set default status to "Available" if not provided
            if (!isset($vehicleData['status']) || empty($vehicleData['status'])) {
                $vehicleData['status'] = Vehicle::STATUS_AVAILABLE;
            }
            
            // Handle main image upload if present
            if ($request->hasFile('vehicle_image')) {
                $image = $request->file('vehicle_image');
                $filename = $validated['stock_number'] . '_main_' . time() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('vehicles', $filename, 'public');
                $vehicleData['image_path'] = $path;
            }
            
            // Remove vehicle_image from data array as it's not a column in the database
            if (isset($vehicleData['vehicle_image'])) {
                unset($vehicleData['vehicle_image']);
            }
            
            // Remove gallery_images from data array
            if (isset($vehicleData['gallery_images'])) {
                unset($vehicleData['gallery_images']);
            }
            
            // Create the vehicle
            $vehicle = Vehicle::create($vehicleData);
            
            // Handle gallery images if present
            if ($request->hasFile('gallery_images')) {
                $sortOrder = 0;
                foreach ($request->file('gallery_images') as $image) {
                    $sortOrder++;
                    $filename = $validated['stock_number'] . '_gallery_' . time() . '_' . $sortOrder . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('vehicles/gallery', $filename, 'public');
                    
                    $vehicle->images()->create([
                        'image_url' => 'vehicles/gallery/' . $filename,
                        'sort_order' => $sortOrder,
                        'is_featured' => ($sortOrder === 1 && !$vehicle->image_path), // Make featured if it's the first image and there's no main image
                    ]);
                }
            }
            
            // Get users with Admin role
            $admins = User::role('Admin')->get();
            
            // Get users with Sales Manager role
            $salesManagers = User::role('Sales Manager')->get();
            
            // Get users with Recon Manager role
            $reconManagers = User::role('Recon Manager')->get();
            
            // Combine all users to notify
            $managers = $admins->merge($salesManagers)->merge($reconManagers);
            
            if ($managers->isEmpty()) {
                \Log::warning("No Admins, Sales or Recon Managers found to notify about new vehicle {$vehicle->stock_number}");
            } else {
                foreach ($managers as $manager) {
                    $manager->notify(new NewVehicleArrival($vehicle));
                }
                \Log::info("Sent notifications about new vehicle {$vehicle->stock_number} to " . $managers->count() . " users");
            }
            
            DB::commit();
            
            // Store the new vehicle ID in the session
            session()->flash('new_vehicle_id', $vehicle->id);
            
            // Broadcast new vehicle event for real-time notification
            Log::info('Dispatching NewVehicleEvent for newly created vehicle', [
                'vehicle_id' => $vehicle->id,
                'stock_number' => $vehicle->stock_number,
            ]);
            broadcast(new NewVehicleEvent(1, [$vehicle->id]))->toOthers();
            
            return redirect()->route('vehicles.index')
                ->with('success', 'Vehicle created successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while creating the vehicle: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $vehicle = Vehicle::findOrFail($id);
        
        // For transporters, verify they have access to this vehicle
        if (auth()->user()->hasRole('Transporter')) {
            $hasAccess = $vehicle->transports()
                ->where('transporter_id', auth()->user()->transporter_id)
                ->orWhereHas('batch', function ($query) {
                    $query->where('transporter_id', auth()->user()->transporter_id);
                })
                ->exists();
            
            if (!$hasAccess) {
                abort(403, 'You do not have access to this vehicle');
            }
        }
        
        // Mark this vehicle as read by the current user
        $vehicle->markAsRead();
        
        return view('vehicles.show', compact('vehicle'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $this->authorize('edit vehicles');
        $vehicle = Vehicle::findOrFail($id);
        return view('vehicles.edit', compact('vehicle'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->authorize('edit vehicles');
        
        $vehicle = Vehicle::findOrFail($id);
        
        $validated = $request->validate([
            'stock_number' => 'required|string|max:255|unique:vehicles,stock_number,' . $id,
            'vin' => 'required|string|max:255|unique:vehicles,vin,' . $id,
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'trim' => 'nullable|string|max:255',
            'date_in_stock' => 'nullable|date',
            'odometer' => 'nullable|integer',
            'exterior_color' => 'nullable|string|max:255',
            'interior_color' => 'nullable|string|max:255',
            'status' => 'nullable|string|max:255',
            'body_type' => 'nullable|string|max:255',
            'drive_train' => 'nullable|string|max:255',
            'engine' => 'nullable|string|max:255',
            'fuel_type' => 'nullable|string|max:255',
            'is_featured' => 'boolean',
            'transmission' => 'nullable|string|max:255',
            'transmission_type' => 'nullable|string|max:255',
            'advertising_price' => 'nullable|numeric',
            'vehicle_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'gallery_images' => 'nullable|array',
            'gallery_images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
        
        DB::beginTransaction();
        
        try {
            $vehicleData = $validated;
            
            // Handle main image upload if present
            if ($request->hasFile('vehicle_image')) {
                // Remove old image if exists
                if ($vehicle->image_path && Storage::disk('public')->exists($vehicle->image_path)) {
                    Storage::disk('public')->delete($vehicle->image_path);
                }
                
                $image = $request->file('vehicle_image');
                $filename = $vehicleData['stock_number'] . '_main_' . time() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('vehicles', $filename, 'public');
                $vehicleData['image_path'] = $path;
            }
            
            // Remove vehicle_image from data array as it's not a column in the database
            if (isset($vehicleData['vehicle_image'])) {
                unset($vehicleData['vehicle_image']);
            }
            
            // Remove gallery_images from data array
            if (isset($vehicleData['gallery_images'])) {
                unset($vehicleData['gallery_images']);
            }
            
            $vehicle->update($vehicleData);
            
            // Handle gallery images if present
            if ($request->hasFile('gallery_images')) {
                $sortOrder = $vehicle->images()->max('sort_order') ?? 0;
                
                foreach ($request->file('gallery_images') as $image) {
                    $sortOrder++;
                    $filename = $vehicleData['stock_number'] . '_gallery_' . time() . '_' . $sortOrder . '.' . $image->getClientOriginalExtension();
                    $path = $image->storeAs('vehicles/gallery', $filename, 'public');
                    
                    $vehicle->images()->create([
                        'image_url' => 'vehicles/gallery/' . $filename,
                        'sort_order' => $sortOrder,
                        'is_featured' => false, // Don't make newly added images featured during an update
                    ]);
                }
            }
            
            DB::commit();
            
            // Store the updated vehicle ID in the session
            session()->flash('updated_vehicle_id', $vehicle->id);
            
            // Broadcast vehicle update event for real-time notification
            Log::info('Dispatching NewVehicleEvent for updated vehicle', [
                'vehicle_id' => $vehicle->id,
                'stock_number' => $vehicle->stock_number,
            ]);
            broadcast(new NewVehicleEvent(1, [$vehicle->id], 'update'))->toOthers();
            
            return redirect()->route('vehicles.index')
                ->with('success', 'Vehicle updated successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while updating the vehicle: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->authorize('delete vehicles');
        
        $vehicle = Vehicle::findOrFail($id);
        
        // Delete the image if exists
        if ($vehicle->image_path && Storage::disk('public')->exists($vehicle->image_path)) {
            Storage::disk('public')->delete($vehicle->image_path);
        }
        
        // Delete the vehicle
        $vehicle->delete();
        
        return redirect()->route('vehicles.index')
            ->with('success', 'Vehicle deleted successfully');
    }
    
    /**
     * Upload additional images for a vehicle
     */
    public function uploadImages(Request $request, string $id)
    {
        $this->authorize('edit vehicles');
        
        $vehicle = Vehicle::findOrFail($id);
        
        $request->validate([
            'images' => 'required|array',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
        
        DB::beginTransaction();
        
        try {
            $sortOrder = $vehicle->images()->max('sort_order') ?? 0;
            
            foreach ($request->file('images') as $image) {
                $sortOrder++;
                $filename = $vehicle->stock_number . '_gallery_' . time() . '_' . $sortOrder . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('vehicles/gallery', $filename, 'public');
                
                $vehicle->images()->create([
                    'image_url' => 'vehicles/gallery/' . $filename,
                    'sort_order' => $sortOrder,
                    'is_featured' => false,
                ]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true, 
                'message' => count($request->file('images')) . ' images uploaded successfully'
            ]);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while uploading images: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Delete a vehicle image
     */
    public function deleteImage(Request $request, string $vehicleId, string $imageId)
    {
        $this->authorize('edit vehicles');
        
        $vehicle = Vehicle::findOrFail($vehicleId);
        $image = $vehicle->images()->findOrFail($imageId);
        
        DB::beginTransaction();
        
        try {
            // Delete the image file from storage
            if (Storage::disk('public')->exists($image->image_url)) {
                Storage::disk('public')->delete($image->image_url);
            }
            
            // Delete the image record
            $image->delete();
            
            // If this was the featured image, set a new featured image if available
            if ($image->is_featured) {
                $newFeatured = $vehicle->images()->first();
                if ($newFeatured) {
                    $newFeatured->update(['is_featured' => true]);
                }
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully'
            ]);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the image: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Update the order of vehicle images
     */
    public function updateImageOrder(Request $request, string $vehicleId)
    {
        $this->authorize('edit vehicles');
        
        $vehicle = Vehicle::findOrFail($vehicleId);
        
        $request->validate([
            'images' => 'required|array',
            'images.*.id' => 'required|exists:vehicle_images,id',
            'images.*.sort_order' => 'required|integer|min:0',
        ]);
        
        DB::beginTransaction();
        
        try {
            foreach ($request->images as $imageData) {
                $image = $vehicle->images()->findOrFail($imageData['id']);
                $image->update(['sort_order' => $imageData['sort_order']]);
            }
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Image order updated successfully'
            ]);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating image order: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Set a vehicle image as featured
     */
    public function setFeaturedImage(Request $request, string $vehicleId, string $imageId)
    {
        $this->authorize('edit vehicles');
        
        $vehicle = Vehicle::findOrFail($vehicleId);
        $image = $vehicle->images()->findOrFail($imageId);
        
        DB::beginTransaction();
        
        try {
            // Remove featured flag from all images of this vehicle
            $vehicle->images()->update(['is_featured' => false]);
            
            // Set this image as featured
            $image->update(['is_featured' => true]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Featured image updated successfully'
            ]);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while setting featured image: ' . $e->getMessage()
            ], 500);
        }
    }
} 