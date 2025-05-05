<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\TransporterSeeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       
        $this->call(RoleAndPermissionSeeder::class);
        
        // Seed users with specific roles
        $this->call(UserRoleSeeder::class);

        $this->call([        
            PermissionSeeder::class,
           // TransporterSeeder::class,
        ]);

        // Inspection & Repair Seeders
        // $this->call([
        //     VendorTypeSeeder::class,
        //     VendorSeeder::class,
        //     InspectionStageSeeder::class,
        //     InspectionItemSeeder::class,
        // ]);
    }
}
