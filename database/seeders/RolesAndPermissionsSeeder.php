<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Role and Permission Management
            'view roles',
            'create roles',
            'edit roles',
            'delete roles',
            'assign roles',
            'view permissions',
            'create permissions',
            'edit permissions',
            'delete permissions',
            'assign permissions',
            'manage roles and permissions',
            
            // Vehicle permissions
            'view vehicles',
            'create vehicles',
            'edit vehicles',
            'delete vehicles',
            'archive vehicles',
            'bulk edit vehicles',
            'scan vehicle barcodes',
            
            // Transport permissions
            'view transports',
            'create transports',
            'edit transports',
            'delete transports',
            'manage transports',
            
            // Sales Issues permissions
            'view sales issues',
            'create sales issues',
            'edit sales issues',
            'delete sales issues',
            'review sales issues',
            'manage sales issues',
            'assign sales issues',
            'resolve sales issues',
            'export sales issues',
            
            // Goodwill Claims permissions
            'view goodwill claims',
            'create goodwill claims',
            'edit goodwill claims',
            'delete goodwill claims',
            'approve goodwill claims',
            'reject goodwill claims',
            'update goodwill claims',
            
            // Tags permissions
            'view tags',
            'create tags',
            'edit tags',
            'delete tags',
            'assign tags',
            
            // Timeline permissions
            'view timeline',
            'add timeline entries',
            
            // Alerts permissions
            'view alerts',
            'create alerts',
            'edit alerts',
            'delete alerts',
            
            // Photos permissions
            'view photos',
            'upload photos',
            'delete photos',
            
            // Ready-to-post checklist permissions
            'view checklists',
            'create checklists',
            'complete checklists',
            
            // Notifications permissions
            'view notifications',
            'manage notification settings',
            
            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            'assign roles',
            
            // Sales Team Management permissions
            'view sales team',
            'add sales team members',
            'edit sales team members',
            'remove sales team members',
            'manage sales team',
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Define roles and their permissions
        $roles = [
            'Admin' => $permissions, // Admin gets all permissions
            'Sales Manager' => [
                // Vehicle permissions
                'view vehicles', 'create vehicles', 'edit vehicles', 'delete vehicles', 'archive vehicles', 'bulk edit vehicles', 'scan vehicle barcodes',
                
                // Transport permissions
                'view transports', 'create transports', 'edit transports', 'delete transports', 'manage transports',
                
                // Sales Issues - all permissions
                'view sales issues', 'create sales issues', 'edit sales issues', 'delete sales issues',
                'review sales issues', 'manage sales issues', 'assign sales issues', 'resolve sales issues', 'export sales issues',
                
                // Goodwill Claims - all permissions
                'view goodwill claims', 'create goodwill claims', 'edit goodwill claims', 'delete goodwill claims',
                'approve goodwill claims', 'reject goodwill claims', 'update goodwill claims',
                
                // Tags permissions
                'view tags', 'create tags', 'edit tags', 'delete tags', 'assign tags',
                
                // Timeline permissions
                'view timeline', 'add timeline entries',
                
                // Alerts permissions
                'view alerts', 'create alerts', 'edit alerts', 'delete alerts',
                
                // Photos permissions
                'view photos', 'upload photos', 'delete photos',
                
                // Notifications permissions
                'view notifications', 'manage notification settings',
                
                // User and Team management
                'view users', 'edit users',
                'view sales team', 'add sales team members', 'edit sales team members', 'remove sales team members', 'manage sales team'
            ],
            'Recon Manager' => [
                'view vehicles', 'edit vehicles', 'archive vehicles', 'bulk edit vehicles',
                'view transports', 'create transports', 'edit transports', 'manage transports',
                'view sales issues', 'edit sales issues',
                'view goodwill claims', 'edit goodwill claims',
                'view tags', 'assign tags',
                'view timeline', 'add timeline entries',
                'view alerts', 'create alerts',
                'view photos', 'upload photos',
                'view checklists', 'create checklists', 'complete checklists',
                'view notifications', 'manage notification settings'
            ],
            'Transporter' => [
                'view vehicles',
                'view timeline',
                'add timeline entries',
                'view photos',
                'upload photos',
                'view notifications'
            ],
            'Vendor' => [
                'view vehicles',
                'view timeline',
                'view photos',
                'upload photos',
                'view checklists',
                'complete checklists',
                'view notifications'
            ],
            'Sales Team' => [
                'view vehicles',
                'view sales issues',
                'create sales issues',
                'edit sales issues',
                'resolve sales issues',
                'view goodwill claims',
                'create goodwill claims',
                'view timeline',
                'add timeline entries',
                'view photos',
                'upload photos',
                'view notifications'
            ]
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $rolePermissions) {
            // Create role if it doesn't exist
            $role = Role::firstOrCreate(['name' => $roleName]);
            
            // Get all the permissions objects
            $permissionsToSync = Permission::whereIn('name', $rolePermissions)->get();
            
            // Sync permissions to role
            $role->syncPermissions($permissionsToSync);
        }

        // Assign admin role to first user if exists
        $user = User::first();
        if ($user && !$user->hasRole('admin')) {
            $user->assignRole('admin');
        }
    }
}