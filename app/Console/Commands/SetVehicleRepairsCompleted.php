<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vehicle;

class SetVehicleRepairsCompleted extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vehicle:set-repairs-completed {stock_number}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set a vehicle status to repairs_completed for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $stockNumber = $this->argument('stock_number');
        
        $vehicle = Vehicle::where('stock_number', $stockNumber)->first();
        
        if (!$vehicle) {
            $this->error("Vehicle with stock number {$stockNumber} not found.");
            return Command::FAILURE;
        }
        
        $vehicle->update(['status' => Vehicle::STATUS_REPAIRS_COMPLETED]);
        
        $this->info("Vehicle {$stockNumber} status set to 'repairs_completed'");
        
        return Command::SUCCESS;
    }
} 