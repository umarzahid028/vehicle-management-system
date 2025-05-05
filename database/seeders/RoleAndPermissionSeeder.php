<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions
        $permissions = [
            // Basic Permissions
            'view any',
            'view own',
            'create',
            'edit',
            'delete',
            'manage',

            // User Management
            'view users',
            'create users',
            'edit users',
            'delete users',
            'manage users',

            // Role & Permission Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'assign roles',
            'view permissions',
            'manage permissions',

            // Sales Team permissions
            'view sales team',
            'create sales team',
            'edit sales team',
            'delete sales team',
            'manage sales team',

            // Vehicle Management
            'view vehicles',
            'create vehicles',
            'edit vehicles',
            'delete vehicles',
            'manage vehicles',
            'archive vehicles',
            'bulk edit vehicles',

            // Transport Management
            'view transports',
            'create transports',
            'edit transports',
            'delete transports',
            'manage transports',

            // Inspection Management
            'view inspections',
            'create inspections',
            'edit inspections',
            'delete inspections',
            'manage inspections',
            'configure stages',

            // Vendor Management
            'view vendors',
            'create vendors',
            'edit vendors',
            'delete vendors',
            'manage vendors',

            // Sales Issues
            'view sales issues',
            'create sales issues',
            'edit sales issues',
            'delete sales issues',
            'manage sales issues',
            'review sales issues',

            // Goodwill Claims
            'view goodwill claims',
            'create goodwill claims',
            'edit goodwill claims',
            'delete goodwill claims',
            'manage goodwill claims',
            'approve goodwill claims',
            'reject goodwill claims',

            // Additional Features
            'view timeline',
            'add timeline entries',
            'manage timeline',
            'view tags',
            'create tags',
            'edit tags',
            'delete tags',
            'assign tags',
            'view alerts',
            'create alerts',
            'manage alerts',
            'view photos',
            'upload photos',
            'delete photos',
            'manage photos',
            'view checklists',
            'create checklists',
            'edit checklists',
            'delete checklists',
            'complete checklists',
            'manage checklists',
            'view notifications',
            'manage notifications',
            'manage notification settings',
            'view reports',
            'generate reports',
            'export data',
            'manage system settings'
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create or update roles with guard_name
        $roles = [
            'Admin' => $permissions, // Admin gets all permissions
            'Sales Manager' => [
                // Sales Team permissions (full access)
                'view sales team',
                'create sales team',
                'edit sales team',
                'delete sales team',
                'manage sales team',
                
                // Basic permissions
                'view any',
                'view own',
                'create',
                'edit',
                'delete',
                'manage',
                
                // Sales related permissions
                'view sales issues',
                'create sales issues',
                'edit sales issues',
                'delete sales issues',
                'manage sales issues',
                
                'view goodwill claims',
                'create goodwill claims',
                'edit goodwill claims',
                'delete goodwill claims',
                'manage goodwill claims',
                
                // Vehicle related
                'view vehicles',
                'create vehicles',
                'edit vehicles',
                'delete vehicles',
                'manage vehicles',
                
                // Inspection related
                'view inspections',
                'create inspections',
                'edit inspections',
                'delete inspections',
                'manage inspections',
            ],
            'Recon Manager' => [
                // Sales Team permissions (full access)
                'view sales team',
                'create sales team',
                'edit sales team',
                'delete sales team',
                'manage sales team',
                
                // Basic permissions
                'view any',
                'view own',
                'create',
                'edit',
                'delete',
                'manage',
                
                // Vehicle and Inspection permissions
                'view vehicles',
                'create vehicles',
                'edit vehicles',
                'delete vehicles',
                'manage vehicles',
                
                'view inspections',
                'create inspections',
                'edit inspections',
                'delete inspections',
                'manage inspections',
                
                // Vendor permissions
                'view vendors',
                'create vendors',
                'edit vendors',
                'delete vendors',
                'manage vendors',
            ],
            'Sales Team' => [
                // Basic sales team access
                'view sales team',
                'view vehicles',
                
                // Sales Issues - Full Access
                'view sales issues',
                'create sales issues',
                'edit sales issues',
                'delete sales issues',
                'manage sales issues',
                'review sales issues',
                
                // Goodwill Claims - Full Access
                'view goodwill claims',
                'create goodwill claims',
                'edit goodwill claims',
                'delete goodwill claims',
                'manage goodwill claims',
                'approve goodwill claims',
                'reject goodwill claims',
            ],
            'Vendor' => [
                'view vehicles',
                'view inspections',
                'create inspections',
                'edit inspections',
            ],
            'Transporter' => [
                // Basic permissions for transporters
                'view own',
                'view vehicles',
                'view transports',
                'create transports',
                'edit transports',
                'view inspections',
            ]
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
            
            $this->command->info("\nRole '{$roleName}' created with " . count($rolePermissions) . " permissions:");
            foreach ($rolePermissions as $permission) {
                $this->command->info("- $permission");
            }
        }

        // Ensure admin users have the admin role
        $adminUsers = User::whereHas('roles', function($query) {
            $query->where('name', 'Admin');
        })->get();

        foreach ($adminUsers as $admin) {
            if (!$admin->hasRole('Admin')) {
                $admin->assignRole('Admin');
            }
        }
    }
} 