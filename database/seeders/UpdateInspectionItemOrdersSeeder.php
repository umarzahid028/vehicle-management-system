<?php

namespace Database\Seeders;

use App\Models\InspectionItem;
use App\Models\InspectionStage;
use Illuminate\Database\Seeder;

class UpdateInspectionItemOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds to populate order values for existing inspection items
     */
    public function run(): void
    {
        // Get all stages
        $stages = InspectionStage::all();
        
        foreach ($stages as $stage) {
            // Get all items for this stage
            $items = InspectionItem::where('inspection_stage_id', $stage->id)
                ->orderBy('id')
                ->get();
            
            // Update each item with an incremental order
            $order = 1;
            foreach ($items as $item) {
                $item->update(['order' => $order]);
                $order++;
            }
            
            $this->command->info("Updated order for {$items->count()} items in stage: {$stage->name}");
        }
    }
} 