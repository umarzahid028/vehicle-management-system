<?php

namespace Database\Seeders;

use App\Models\InspectionStage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InspectionStageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $stages = [
            [
                'name' => 'Performance Test Drive',
                'description' => 'Evaluate transmission, engine, suspension, 4x4 functionality, and steering.',
                'order' => 1,
            ],
            [
                'name' => 'Arbitration Bucket',
                'description' => 'Identify and flag any issues that may be eligible for claims or arbitration.',
                'order' => 2,
            ],
            [
                'name' => 'Diagnostic/Mechanical Repair',
                'description' => 'Assign the vehicle to a vendor for mechanical repairs. Log repair costs and attach supporting photos.',
                'order' => 3,
            ],
            [
                'name' => 'Exterior Work',
                'description' => 'Address required work such as Paintless Dent Repair (PDR), painting, or touch-ups.',
                'order' => 4,
            ],
            [
                'name' => 'Interior Work',
                'description' => 'Inspect and repair upholstery, radio, dashboard, and steering wheel.',
                'order' => 5,
            ],
            [
                'name' => 'Idle/Feature Check',
                'description' => 'Ensure the functionality of lights, wipers, air conditioning, horn, windows, locks, etc.',
                'order' => 6,
            ],
            [
                'name' => 'Tires, Brakes, and Fluids',
                'description' => 'Check tires, brakes, and all fluid levels. Assign work to either an internal team or an external vendor.',
                'order' => 7,
            ],
        ];

        foreach ($stages as $stage) {
            InspectionStage::updateOrCreate(
                ['name' => $stage['name']],
                [
                    'slug' => Str::slug($stage['name']),
                    'description' => $stage['description'],
                    'order' => $stage['order'],
                    'is_active' => true,
                ]
            );
        }
    }
} 