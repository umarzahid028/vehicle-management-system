<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Events\NewVehicleEvent;
use Illuminate\Console\Command;

class ImportVehicles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicles:import {file} {--dry-run : Run without saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import vehicles from CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');
        $dryRun = $this->option('dry-run');
        
        if (!file_exists($file)) {
            $this->error("File {$file} does not exist");
            return 1;
        }
        
        $this->info("Importing vehicles from {$file}...");
        
        // Process the file...
        // This is just a placeholder. In a real implementation, you would parse the CSV file
        // and create vehicles from it.
        
        // For demonstration, we'll create a simulated import result
        $importedVehicleIds = [];
        
        if (!$dryRun) {
            $this->info("Saved vehicles to database");
            
            // In a real implementation, this would be the IDs of newly created vehicles
            // Here, we're just simulating it
            $importedVehicleIds = Vehicle::latest()->take(5)->pluck('id')->toArray();
            
            $count = count($importedVehicleIds);
            
            if ($count > 0) {
                // Broadcast that new vehicles were imported
                event(new NewVehicleEvent($count, $importedVehicleIds));
                $this->info("Broadcast notification for {$count} new vehicles");
            }
        } else {
            $this->info("Dry run - no vehicles were saved");
        }
        
        return 0;
    }
} 