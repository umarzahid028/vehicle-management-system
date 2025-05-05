<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Events\NewVehiclesImported;
use Illuminate\Support\Facades\Log;

class TestBroadcast extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:broadcast';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test broadcasting from command line';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Testing broadcast from command line...");
        
        try {
            broadcast(new NewVehiclesImported([
                'new_count' => 1, 
                'modified_count' => 1,
                'message' => 'Test from command line',
                'timestamp' => now()->timestamp
            ]))->toOthers();
            
            $this->info("Broadcast sent successfully!");
            Log::info("Test broadcast sent from command line");
        } catch (\Exception $e) {
            $this->error("Error broadcasting: " . $e->getMessage());
            Log::error("Broadcast test error: " . $e->getMessage());
        }
        
        return 0;
    }
} 