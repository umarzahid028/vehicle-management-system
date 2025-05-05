<?php

namespace App\Console\Commands;

use App\Services\VehicleImportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Events\NewVehiclesImported;
use App\Events\NewVehicleEvent;

class ImportVehiclesCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:vehicles-csv 
                            {--directory= : Directory path to look for CSV files (default: storage/app/vehicle-imports)}
                            {--file= : Specific file to import}
                            {--no-notifications : Disable sending notifications}
                            {--archive : Move processed files to archive directory}
                            {--debug : Show detailed error messages}
                            {--dryrun : Don\'t save to database, just validate}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import vehicles from CSV files in a specified directory';

    /**
     * Execute the console command.
     */
    public function handle(VehicleImportService $importService)
    {
        Log::info("Importing vehicles from CSV files in directory:");
        // Get command options
        $specificFile = $this->option('file');
        $sendNotifications = !$this->option('no-notifications');
        $shouldArchive = $this->option('archive');
        $debug = $this->option('debug');
        $dryRun = $this->option('dryrun');
        
        // Determine directory path
        $directory = $this->option('directory') ?: storage_path('app/vehicle-imports');
        
        Log::info("Importing vehicles from CSV files in directory: {$directory}");

        // Ensure directory exists
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
            $this->info("Created directory: {$directory}");
        }
        
        // Ensure archive directory exists if needed
        $archiveDir = $directory . '/archive';
        if ($shouldArchive && !File::exists($archiveDir)) {
            File::makeDirectory($archiveDir, 0755, true);
            $this->info("Created archive directory: {$archiveDir}");
        }
        
        // Get files to process
        if ($specificFile) {
            $filePath = $directory . '/' . $specificFile;
            if (!File::exists($filePath)) {
                $this->error("File not found: {$filePath}");
                return 1;
            }
            
            $files = [$filePath];
        } else {
            // Get all CSV files in the directory
            $files = File::glob($directory . '/*.csv');
        }
        
        if (empty($files)) {
            $this->info("No CSV files found to process.");
            return 0;
        }
        
        $this->info("Found " . count($files) . " file(s) to process.");
        
        $totalImported = 0;
        $totalSkipped = 0;
        $totalErrors = 0;
        $totalNew = 0;
        $totalModified = 0;
        
        // Configure the service
        $importService->setDebug($debug);
        $importService->setDryRun($dryRun);
        
        // Override logger for command output
        $importService->setLogger(function($level, $message, $context = []) use ($debug) {
            switch ($level) {
                case 'error':
                    $this->error($message);
                    if ($debug && !empty($context)) {
                        $this->line(json_encode($context, JSON_PRETTY_PRINT));
                    }
                    break;
                case 'warning':
                    $this->warn($message);
                    if ($debug && !empty($context)) {
                        $this->line(json_encode($context, JSON_PRETTY_PRINT));
                    }
                    break;
                case 'info':
                    $this->info($message);
                    break;
                case 'debug':
                    if ($debug) {
                        $this->comment($message);
                        if (!empty($context)) {
                            $this->line(json_encode($context, JSON_PRETTY_PRINT));
                        }
                    }
                    break;
            }
        });
        
        $newVehicles = [];
        $modifiedVehicles = [];
        
        foreach ($files as $filePath) {
            $fileName = basename($filePath);
            $this->info("Processing file: {$fileName}");
            
            try {
                // Process the file (directly, without queueing)
                $result = $importService->processCsvFile($filePath, $sendNotifications);
                
                // Log the result
                if ($result['success']) {
                    $this->info("Processed {$fileName}: {$result['imported']} imported, {$result['skipped']} skipped, {$result['errors']} errors.");
                    
                    // Track new and modified vehicles
                    if (isset($result['new_vehicles']) && count($result['new_vehicles']) > 0) {
                        $totalNew += count($result['new_vehicles']);
                        $newVehicles = array_merge($newVehicles, $result['new_vehicles']);
                    }
                    
                    if (isset($result['modified_vehicles']) && count($result['modified_vehicles']) > 0) {
                        $totalModified += count($result['modified_vehicles']);
                        $modifiedVehicles = array_merge($modifiedVehicles, $result['modified_vehicles']);
                    }
                    
                    // Archive file if requested
                    if ($shouldArchive && !$dryRun) {
                        $archivePath = $archiveDir . '/' . $fileName . '.' . date('Ymd_His');
                        File::move($filePath, $archivePath);
                        $this->info("Archived file to: {$archivePath}");
                    }
                } else {
                    $this->error("Failed to process {$fileName}: {$result['message']}");
                }
                
                // Update totals
                $totalImported += $result['imported'];
                $totalSkipped += $result['skipped'];
                $totalErrors += $result['errors'];
            } catch (\Exception $e) {
                $this->error("Exception while processing file {$fileName}: " . $e->getMessage());
                if ($debug) {
                    $this->comment("Trace: " . $e->getTraceAsString());
                }
                $totalErrors++;
            }
        }
        
        $this->info("Import complete. Total: {$totalImported} imported, {$totalSkipped} skipped, {$totalErrors} errors.");
        $this->info("New vehicles: {$totalNew}, Modified vehicles: {$totalModified}");
        
        // Trigger real-time notification if there are new or modified vehicles
        if (($totalNew > 0 || $totalModified > 0) && !$dryRun) {
            try {
                $this->info("Broadcasting notification for new/modified vehicles...");
                
                // Create event data
                $eventData = [
                    'new_count' => $totalNew,
                    'modified_count' => $totalModified,
                    'new_vehicles' => $newVehicles,
                    'modified_vehicles' => $modifiedVehicles,
                    'message' => "Import complete: {$totalNew} new vehicles, {$totalModified} modified vehicles",
                    'timestamp' => now()->timestamp,
                    'source' => 'command-line'
                ];
                
                // Try both broadcast methods to ensure it works in all environments
                $event = new NewVehiclesImported($eventData);
                
                // Method 1: Direct broadcast
                broadcast($event)->toOthers();
                
                // Method 2: Event helper (for compatibility)
                event($event);
                
                // Also trigger the NewVehicleEvent for sound notification compatibility
                if ($totalNew > 0) {
                    // Get an array of vehicle IDs
                    $vehicleIds = collect($newVehicles)->pluck('id')->toArray();
                    // Only send one event with all vehicle IDs, not one per vehicle
                    broadcast(new NewVehicleEvent($totalNew, $vehicleIds))->toOthers();
                    $this->info("Broadcast NewVehicleEvent for sound notification");
                }
                
                $this->info("Broadcast completed successfully.");
                
                // For debugging, write to the log file as well
                Log::info("Vehicle import notification broadcast", $eventData);
            } catch (\Exception $e) {
                $this->error("Error broadcasting notification: " . $e->getMessage());
                if ($debug) {
                    $this->comment("Trace: " . $e->getTraceAsString());
                }
            }
        }
        
        if ($dryRun) {
            $this->comment("This was a dry run - no data was saved to the database.");
        }
        
        return 0;
    }
}
