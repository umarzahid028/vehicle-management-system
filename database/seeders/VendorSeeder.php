<?php

namespace Database\Seeders;

use App\Models\Vendor;
use Illuminate\Database\Seeder;

class VendorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vendors = [
            [
                'name' => 'Quick Mechanic Services',
                'contact_person' => 'John Smith',
                'email' => 'john@quickmechanic.com',
                'phone' => '555-123-4567',
                'type' => 'mechanical',
            ],
            [
                'name' => 'Auto Body Experts',
                'contact_person' => 'Lisa Johnson',
                'email' => 'lisa@autobody.com',
                'phone' => '555-987-6543',
                'type' => 'body_shop',
            ],
            [
                'name' => 'Professional Detailers',
                'contact_person' => 'Mike Wilson',
                'email' => 'mike@prodetail.com',
                'phone' => '555-456-7890',
                'type' => 'detail',
            ],
            [
                'name' => 'Tire Masters',
                'contact_person' => 'Sarah Davis',
                'email' => 'sarah@tiremasters.com',
                'phone' => '555-234-5678',
                'type' => 'tire',
            ],
            [
                'name' => 'Luxury Auto Upholstery',
                'contact_person' => 'David Brown',
                'email' => 'david@luxuryupholstery.com',
                'phone' => '555-345-6789',
                'type' => 'upholstery',
            ],
            [
                'name' => 'Clear Vision Glass Repairs',
                'contact_person' => 'Jessica Martinez',
                'email' => 'jessica@clearvisionglass.com',
                'phone' => '555-876-5432',
                'type' => 'glass',
            ],
            [
                'name' => 'All-In-One Auto Services',
                'contact_person' => 'Robert Taylor',
                'email' => 'robert@allinauto.com',
                'phone' => '555-765-4321',
                'type' => 'other',
            ],
        ];

        foreach ($vendors as $vendor) {
            Vendor::updateOrCreate(
                ['name' => $vendor['name']],
                [
                    'contact_person' => $vendor['contact_person'],
                    'email' => $vendor['email'],
                    'phone' => $vendor['phone'],
                    'specialty_tags' => $vendor['type'],
                    'is_active' => true,
                ]
            );
        }
    }
} 