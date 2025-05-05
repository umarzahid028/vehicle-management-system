<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //$this->authorize('assign roles');
        
        $permissions = Permission::query();
        
        if ($request->has('search')) {
            $search = $request->input('search');
            $permissions->where('name', 'like', "%{$search}%");
        }
        
        $permissionGroups = [];
        $permissionsList = $permissions->get();
        
        foreach ($permissionsList as $permission) {
            $parts = explode(' ', $permission->name);
            $action = $parts[0];
            $resource = implode(' ', array_slice($parts, 1));
            
            if (!isset($permissionGroups[$resource])) {
                $permissionGroups[$resource] = [];
            }
            
            $permissionGroups[$resource][] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'action' => $action
            ];
        }
        
        // Sort groups alphabetically
        ksort($permissionGroups);
        
        return view('permissions.index', compact('permissionGroups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //$this->authorize('assign roles');
        
        // Get distinct resource names from existing permissions
        $resources = $this->getDistinctResources();
        
        // Common action types that can be performed on resources
        $actions = ['view', 'create', 'edit', 'delete', 'manage'];
        
        return view('permissions.create', compact('resources', 'actions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //$this->authorize('assign roles');
        
        $this->validate($request, [
            'name' => 'nullable|string|max:255',
            'resource' => 'required_without:name|string|max:255',
            'actions' => 'required_without:name|array',
            'actions.*' => 'string|in:view,create,edit,delete,manage',
        ]);
        
        DB::beginTransaction();
        
        try {
            if ($request->filled('name')) {
                // Direct permission name provided
                $permissionName = trim($request->input('name'));
                
                if (!Permission::where('name', $permissionName)->exists()) {
                    Permission::create(['name' => $permissionName]);
                }
            } else {
                // Resource and actions provided
                $resource = trim($request->input('resource'));
                $actions = $request->input('actions', []);
                
                foreach ($actions as $action) {
                    $permissionName = $action . ' ' . $resource;
                    
                    if (!Permission::where('name', $permissionName)->exists()) {
                        Permission::create(['name' => $permissionName]);
                    }
                }
            }
            
            DB::commit();
            
            return redirect()->route('admin.permissions.index')
                ->with('success', 'Permission created successfully');
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->back()->withInput()
                ->with('error', 'An error occurred while creating the permission: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //$this->authorize('assign roles');
        
        $permission = Permission::findOrFail($id);
        $roles = $permission->roles;
        
        return view('permissions.show', compact('permission', 'roles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //$this->authorize('assign roles');
        
        $permission = Permission::findOrFail($id);
        
        // Parse the permission name into action and resource parts
        $parts = explode(' ', $permission->name);
        $action = $parts[0];
        $resource = implode(' ', array_slice($parts, 1));
        
        // Get distinct resource names from existing permissions
        $resources = $this->getDistinctResources();
        
        // Common action types that can be performed on resources
        $actions = ['view', 'create', 'edit', 'delete', 'manage'];
        
        return view('permissions.edit', compact('permission', 'action', 'resource', 'resources', 'actions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //$this->authorize('assign roles');
        
        $permission = Permission::findOrFail($id);
        
        $this->validate($request, [
            'name' => 'required|string|max:255|unique:permissions,name,' . $id,
        ]);
        
        // Check if permission is being used by roles
        $roles = $permission->roles;
        
        if ($roles->count() > 0) {
            $roleNames = $roles->pluck('name')->implode(', ');
            return redirect()->back()->withInput()
                ->with('error', "This permission is used by the following roles and cannot be renamed: {$roleNames}");
        }
        
        $permission->name = $request->input('name');
        $permission->save();
        
        DB::commit();
        
        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //$this->authorize('assign roles');
        
        $permission = Permission::findOrFail($id);
        
        // Prevent deletion of built-in permissions
        if (in_array($permission->name, ['manage users', 'manage roles', 'manage permissions'])) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'Cannot delete built-in permissions');
        }
        
        // Check if roles are using this permission
        $hasRoles = DB::table('role_has_permissions')
            ->where('permission_id', $id)
            ->exists();
            
        if ($hasRoles) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'This permission is assigned to roles and cannot be deleted');
        }
        
        // Delete the permission
        $permission->delete();
        
        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permission deleted successfully');
    }
    
    /**
     * Get distinct resource names from existing permissions.
     */
    private function getDistinctResources()
    {
        $resources = [];
        $permissions = Permission::all();
        
        foreach ($permissions as $permission) {
            $parts = explode(' ', $permission->name);
            if (count($parts) > 1) {
                $resource = implode(' ', array_slice($parts, 1));
                if (!in_array($resource, $resources)) {
                    $resources[] = $resource;
                }
            }
        }
        
        sort($resources);
        
        return $resources;
    }
}
