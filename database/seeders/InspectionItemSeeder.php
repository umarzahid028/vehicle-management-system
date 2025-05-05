<?php

namespace Database\Seeders;

use App\Models\InspectionItem;
use App\Models\InspectionStage;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InspectionItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $itemsByStage = [
            'Performance Test Drive' => [
                'Transmission',
                'Engine',
                'Suspension',
                '4x4 Functionality',
                'Steering',
                'Brakes',
                'Acceleration',
                'Noise Level',
            ],
            'Arbitration Bucket' => [
                'Undisclosed Damage',
                'Missing Equipment',
                'Title Issues',
                'Odometer Discrepancy',
                'Frame Damage',
                'Mechanical Issues',
                'Electrical Issues',
            ],
            'Diagnostic/Mechanical Repair' => [
                'Engine Diagnostics',
                'Transmission Service',
                'Electrical System',
                'Cooling System',
                'Exhaust System',
                'Fuel System',
                'Drivetrain',
            ],
            'Exterior Work' => [
                'Paintless Dent Repair',
                'Paint Touch-up',
                'Complete Paint Job',
                'Bumper Repair',
                'Glass Repair/Replacement',
                'Trim Replacement',
                'Body Alignment',
                'Exterior Lighting',
            ],
            'Interior Work' => [
                'Upholstery Repair',
                'Dashboard Repair',
                'Radio/Entertainment System',
                'Steering Wheel',
                'Seat Functionality',
                'Interior Trim',
                'Floor Mats/Carpets',
                'Headliner',
            ],
            'Idle/Feature Check' => [
                'Lights',
                'Wipers',
                'Air Conditioning',
                'Heating',
                'Horn',
                'Windows',
                'Door Locks',
                'Mirrors',
                'Seat Adjustments',
                'Infotainment System',
            ],
            'Tires, Brakes, and Fluids' => [
                'Tire Inspection/Replacement',
                'Brake Pads/Rotors',
                'Brake Fluid',
                'Engine Oil',
                'Transmission Fluid',
                'Coolant',
                'Power Steering Fluid',
                'Windshield Washer Fluid',
                'Differential Fluid',
                'Transfer Case Fluid',
            ],
        ];

        foreach ($itemsByStage as $stageName => $items) {
            $stage = InspectionStage::where('name', $stageName)->first();

            if ($stage) {
                foreach ($items as $itemName) {
                    InspectionItem::updateOrCreate(
                        [
                            'inspection_stage_id' => $stage->id,
                            'name' => $itemName,
                        ],
                        [
                            'slug' => Str::slug($itemName),
                            'description' => "Check and inspect {$itemName}",
                            'is_active' => true,
                        ]
                    );
                }
            }
        }
    }
} 