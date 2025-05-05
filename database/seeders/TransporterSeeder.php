<?php

namespace Database\Seeders;

use App\Models\Transporter;
use Illuminate\Database\Seeder;

class TransporterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $transporters = [
            [
                'name' => 'FastWay Logistics',
                'contact_person' => 'John Smith',
                'phone' => '(555) 123-4567',
                'email' => 'john@fastwaylogistics.com',
                'address' => '123 Highway Ave',
                'city' => 'Houston',
                'state' => 'TX',
                'zip' => '77001',
                'notes' => 'Specializes in truck transport',
                'is_active' => true,
            ],
            [
                'name' => 'Auto Movers Inc',
                'contact_person' => 'Maria Garcia',
                'phone' => '(555) 987-6543',
                'email' => 'mgarcia@automovers.com',
                'address' => '456 Transport Rd',
                'city' => 'Dallas',
                'state' => 'TX',
                'zip' => '75201',
                'notes' => 'Handles multiple vehicles at once',
                'is_active' => true,
            ],
            [
                'name' => 'Premium Car Transport',
                'contact_person' => 'Robert Johnson',
                'phone' => '(555) 456-7890',
                'email' => 'rjohnson@premiumtransport.com',
                'address' => '789 Luxury Lane',
                'city' => 'Austin',
                'state' => 'TX',
                'zip' => '78701',
                'notes' => 'Specializes in luxury and exotic vehicles',
                'is_active' => true,
            ],
        ];

        foreach ($transporters as $transporter) {
            Transporter::create($transporter);
        }
    }
} 