<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create vehicle permissions
        $permissions = [
            // Vehicle permissions
            'view vehicles',
            'create vehicles',
            'edit vehicles',
            'delete vehicles',
            
            // User permissions
            'view users',
            'create users',
            'edit users',
            'delete users',
            
            // Role permissions
            'view roles',
            'assign roles',
            
            // System permissions
            'manage settings',
        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
} 