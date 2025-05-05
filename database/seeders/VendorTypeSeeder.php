<?php

namespace Database\Seeders;

use App\Models\VendorType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VendorTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendorTypes = [
            [
                'name' => 'On-Site Vendor',
                'description' => 'Works at the dealership with full system access',
                'is_on_site' => true,
                'has_system_access' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Off-Site Vendor',
                'description' => 'Works externally, providing cost estimates to the Recon Manager',
                'is_on_site' => false,
                'has_system_access' => false,
                'is_active' => true,
            ],
           
        ];
        
        foreach ($vendorTypes as $type) {
            VendorType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
