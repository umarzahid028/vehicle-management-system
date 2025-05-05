<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\InspectionItemResult;

class FixInspectionRepairStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'inspections:fix-repair-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix inconsistent repair statuses in inspection items';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing inspection repair statuses...');

        // Fix items that are marked as completed but don't have repair_completed set to true
        $completedItemsFixed = InspectionItemResult::where('status', 'completed')
            ->where('repair_completed', false)
            ->update(['repair_completed' => true]);
        
        $this->info("Fixed {$completedItemsFixed} completed items with incorrect repair_completed status");

        // Fix items that have requires_repair but are completed and not marked as repair_completed
        $repairItemsFixed = InspectionItemResult::where('requires_repair', true)
            ->where(function($query) {
                $query->where('status', 'completed')
                    ->orWhere('completed_at', '!=', null);
            })
            ->where('repair_completed', false)
            ->update(['repair_completed' => true]);
        
        $this->info("Fixed {$repairItemsFixed} repair items with incorrect repair_completed status");

        // Check if there are still problematic items
        $problematicItems = InspectionItemResult::where('requires_repair', true)
            ->where('repair_completed', false)
            ->count();

        if ($problematicItems > 0) {
            $this->warn("There are still {$problematicItems} items that require repair but are not marked as completed.");
            $this->warn("These items might be legitimately not completed yet or might need manual review.");
        } else {
            $this->info("All repair items are now correctly marked!");
        }

        return Command::SUCCESS;
    }
}
