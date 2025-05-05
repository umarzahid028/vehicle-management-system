<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
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
        $roles = Role::all();
        $selectedRole = $request->query('role', 'Admin');
        
        $role = Role::with('permissions')
            ->where('name', $selectedRole)
            ->firstOrFail();
            
        $permissions = Permission::all();
        $permissionGroups = $this->groupPermissions($permissions, $role);
        
        return view('roles.index', compact('roles', 'role', 'permissionGroups'));
    }
    
    /**
     * Group permissions by their name prefix (before the first dot).
     */
    private function groupPermissions($permissions, $role)
    {
        $groups = [];
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        
        foreach ($permissions as $permission) {
            $parts = explode(' ', $permission->name);
            $action = $parts[0];
            $resource = !empty(array_slice($parts, 1)) ? implode(' ', array_slice($parts, 1)) : 'general';
            
            if (!isset($groups[$resource])) {
                $groups[$resource] = [];
            }
            
            $groups[$resource][] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'action' => $action,
                'checked' => in_array($permission->name, $rolePermissions)
            ];
        }
        
        // Sort groups alphabetically
        ksort($groups);
        
        return $groups;
    }

    /**
     * Update role permissions
     */
    public function updatePermissions(Request $request)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = Role::where('name', $request->role)->firstOrFail();
        
        // Don't allow modifying the admin role's permissions
        if ($role->name === 'Admin') {
            return redirect()
                ->route('admin.roles.index', ['role' => $role->name])
                ->with('error', 'The Admin role permissions cannot be modified.');
        }

        DB::beginTransaction();
        try {
            $permissions = $request->permissions ?? [];
            // Get permission instances by IDs
            $permissionModels = Permission::whereIn('id', $permissions)->get();
            $role->syncPermissions($permissionModels);
            
            DB::commit();
            
            return redirect()
                ->route('admin.roles.index', ['role' => $role->name])
                ->with('success', 'Permissions updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('admin.roles.index', ['role' => $role->name])
                ->with('error', 'Failed to update permissions: ' . $e->getMessage());
        }
    }
}
