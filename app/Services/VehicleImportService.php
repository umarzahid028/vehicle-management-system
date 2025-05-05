<?php

namespace App\Services;

use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\NewVehicleImported;
use App\Events\NewVehicleEvent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class VehicleImportService
{
    /**
     * Debug mode
     * 
     * @var bool
     */
    protected $debug = false;
    
    /**
     * Dry run mode
     * 
     * @var bool
     */
    protected $dryRun = false;
    
    /**
     * Custom logger
     * 
     * @var callable|null
     */
    protected $logger = null;
    
    /**
     * Set debug mode
     * 
     * @param bool $debug
     * @return self
     */
    public function setDebug(bool $debug): self
    {
        $this->debug = $debug;
        return $this;
    }
    
    /**
     * Set dry run mode
     * 
     * @param bool $dryRun
     * @return self
     */
    public function setDryRun(bool $dryRun): self
    {
        $this->dryRun = $dryRun;
        return $this;
    }
    
    /**
     * Set custom logger
     * 
     * @param callable $logger
     * @return self
     */
    public function setLogger(callable $logger): self
    {
        $this->logger = $logger;
        return $this;
    }
    
    /**
     * Log a message
     * 
     * @param string $level
     * @param string $message
     * @param array $context
     * @return void
     */
    protected function log(string $level, string $message, array $context = []): void
    {
        // Use custom logger if set
        if ($this->logger !== null) {
            call_user_func($this->logger, $level, $message, $context);
        }
        
        // Always log to default logger too
        Log::{$level}($message, $context);
    }
    
    /**
     * Process a CSV file with vehicle data
     *
     * @param string $filePath The full path to the CSV file
     * @param bool $shouldSendNotifications Whether to send notifications or not
     * @return array Summary of import results
     */
    public function processCsvFile(string $filePath, bool $shouldSendNotifications = true): array
    {
        // Get the filename for tracking
        $fileName = basename($filePath);
        
        // Check if file exists and is readable
        if (!file_exists($filePath) || !is_readable($filePath)) {
            $this->log('error', "CSV file not found or not readable: {$filePath}");
            return [
                'success' => false,
                'message' => "File not found or not readable: {$fileName}",
                'imported' => 0,
                'skipped' => 0,
                'errors' => 1,
            ];
        }
        
        // Stats tracking
        $imported = 0;
        $skipped = 0;
        $errors = 0;
        $newVehicles = [];
        $modifiedVehicles = [];
        
        // Open the CSV file
        $handle = fopen($filePath, 'r');
        
        // Get headers from first row of CSV
        $headers = fgetcsv($handle);
        
        if ($this->debug) {
            $this->log('debug', "CSV Headers: ", ['headers' => $headers]);
        }
        
        // Clean and normalize the headers
        $headers = array_map(fn($header) => $this->normalizeHeader($header), $headers);
        
        if ($this->debug) {
            $this->log('debug', "Normalized Headers: ", ['headers' => $headers]);
        }
        
        // Process each row
        $rowCount = 0;
        $vehiclesToNotify = [];
        
        while (($data = fgetcsv($handle)) !== false) {
            $rowCount++;
            
            // Skip empty rows
            if (count(array_filter($data)) === 0) {
                $this->log('debug', "Skipping empty row {$rowCount}");
                continue;
            }
            
            // Skip rows with wrong column count
            if (count($data) !== count($headers)) {
                $this->log('warning', "Row {$rowCount} has different column count than headers. Skipping.", [
                    'headers_count' => count($headers),
                    'data_count' => count($data),
                    'file' => $fileName,
                ]);
                $errors++;
                continue;
            }
            
            // Combine headers with data to create associative array
            $vehicleData = array_combine($headers, $data);
            
            if ($this->debug) {
                $this->log('debug', "Processing row {$rowCount} data: ", ['data' => $vehicleData]);
            }
            
            // Process the vehicle data
            try {
                $result = $this->processVehicleData($vehicleData, $fileName);
                
                if ($result['status'] === 'imported') {
                    $imported++;
                    $this->log('info', "Imported vehicle: {$result['message']}");
                    
                    // Add to notification queue if it's a new vehicle or modified vehicle
                    if ($shouldSendNotifications && !$this->dryRun) {
                        $vehiclesToNotify[] = $result['vehicle'];
                    }
                    
                    // Track if it's a new or modified vehicle
                    if ($result['is_new']) {
                        $newVehicles[] = $result['vehicle'];
                    } else {
                        $modifiedVehicles[] = $result['vehicle'];
                    }
                } elseif ($result['status'] === 'skipped') {
                    $skipped++;
                    $this->log('info', "Skipped vehicle: {$result['message']}");
                }
            } catch (\Exception $e) {
                $this->log('error', "Error processing row {$rowCount}: " . $e->getMessage(), [
                    'file' => $fileName,
                    'data' => $vehicleData,
                    'exception' => $e->getMessage(),
                ]);
                $errors++;
            }
        }
        
        fclose($handle);
        
        // Send a single notification for all vehicles instead of individual ones
        if (!empty($vehiclesToNotify) && $shouldSendNotifications && !$this->dryRun) {
            try {
                // Instead of sending individual notifications, send a batch notification
                $this->log('info', "Sending batch notification for " . count($vehiclesToNotify) . " vehicles");

                // Instead of per-vehicle notification, we'll have the Command handle this
                // through the NewVehiclesImported event
                
                // Broadcast event for new vehicles to trigger sound notification
                if (!empty($newVehicles)) {
                    $newVehicleIds = collect($newVehicles)->pluck('id')->toArray();
                    $newVehicleCount = count($newVehicleIds);
                    
                    $this->log('info', "Broadcasting NewVehicleEvent for {$newVehicleCount} new vehicles");
                    broadcast(new NewVehicleEvent($newVehicleCount, $newVehicleIds))->toOthers();
                }
            } catch (\Exception $e) {
                $this->log('error', "Error sending batch notifications: " . $e->getMessage());
            }
        }
        
        return [
            'success' => true,
            'message' => "Processed file: {$fileName}",
            'imported' => $imported,
            'skipped' => $skipped,
            'errors' => $errors,
            'new_vehicles' => $newVehicles,
            'modified_vehicles' => $modifiedVehicles,
        ];
    }
    
    /**
     * Process individual vehicle data from CSV
     *
     * @param array $data The associative array of vehicle data
     * @param string $fileName The name of the import file
     * @return array Status and vehicle instance
     */
    protected function processVehicleData(array $data, string $fileName): array
    {
        // Map CSV fields to database fields
        $vehicleData = $this->mapFieldsToModel($data);
        
        if ($this->debug) {
            $this->log('debug', "Mapped vehicle data:", ['data' => $vehicleData]);
        }
        
        // Basic validation
        if (empty($vehicleData['stock_number'])) {
            throw new \Exception("Stock number is required");
        }
        
        if (empty($vehicleData['vin'])) {
            throw new \Exception("VIN is required");
        }
        
        // Add import file info
        $vehicleData['import_file'] = $fileName;
        $vehicleData['processed_at'] = now();
        
        // Extract additional images before saving the vehicle
        $additionalImages = $vehicleData['additional_images'] ?? [];
        unset($vehicleData['additional_images']);
        
        // In dry run mode, just validate but don't save
        if ($this->dryRun) {
            return [
                'status' => 'imported',
                'message' => "Dry run: {$vehicleData['year']} {$vehicleData['make']} {$vehicleData['model']} VIN: {$vehicleData['vin']} Stock: {$vehicleData['stock_number']}",
                'vehicle' => new \App\Models\Vehicle($vehicleData),
                'is_new' => true
            ];
        }
        
        // Try to find existing vehicle by stock_number and/or VIN
        $existingVehicle = \App\Models\Vehicle::where('stock_number', $vehicleData['stock_number'])
                                            ->orWhere('vin', $vehicleData['vin'])
                                            ->first();
        
        $isNew = false;
        $wasModified = false;
        $modifiedFields = [];
        
        if ($existingVehicle) {
            // Check if there are changes to the vehicle data
            foreach ($vehicleData as $key => $value) {
                // Skip the import file and processed_at fields
                if (in_array($key, ['import_file', 'processed_at'])) {
                    continue;
                }
                
                // Compare with existing values
                if ($existingVehicle->{$key} != $value) {
                    $modifiedFields[$key] = [
                        'old' => $existingVehicle->{$key},
                        'new' => $value
                    ];
                }
            }
            
            $wasModified = !empty($modifiedFields);
            
            // Update existing vehicle
            if ($wasModified) {
                $existingVehicle->fill($vehicleData);
                $existingVehicle->save();
                
                $result = [
                    'status' => 'imported',
                    'message' => "Updated: {$existingVehicle->year} {$existingVehicle->make} {$existingVehicle->model} VIN: {$existingVehicle->vin} Stock: {$existingVehicle->stock_number}",
                    'vehicle' => $existingVehicle,
                    'is_new' => false,
                    'modified_fields' => $modifiedFields
                ];
            } else {
                // Skip if no changes
                return [
                    'status' => 'skipped',
                    'message' => "No changes: {$existingVehicle->year} {$existingVehicle->make} {$existingVehicle->model} VIN: {$existingVehicle->vin} Stock: {$existingVehicle->stock_number}",
                    'vehicle' => $existingVehicle,
                    'is_new' => false
                ];
            }
        } else {
            // Create new vehicle
            $vehicle = new \App\Models\Vehicle($vehicleData);
            $vehicle->save();
            $isNew = true;
            
            $result = [
                'status' => 'imported',
                'message' => "New: {$vehicle->year} {$vehicle->make} {$vehicle->model} VIN: {$vehicle->vin} Stock: {$vehicle->stock_number}",
                'vehicle' => $vehicle,
                'is_new' => true
            ];
        }
        
        // Process additional images if any
        if (!empty($additionalImages) && isset($result['vehicle'])) {
            $this->processAdditionalImages($result['vehicle'], $additionalImages);
        }
        
        return $result;
    }
    
    /**
     * Map CSV fields to database fields
     *
     * @param array $data The raw CSV data
     * @return array Formatted data for model
     */
    protected function mapFieldsToModel(array $data): array
    {
        $mapped = [];
        
        $mappings = [
            'stocknumber' => 'stock_number',
            'vin' => 'vin',
            'year' => 'year',
            'make' => 'make',
            'model' => 'model',
            'trim' => 'trim',
            'dateinstock' => 'date_in_stock',
            'odometer' => 'odometer',
            'exteriorcolor' => 'exterior_color',
            'interiorcolor' => 'interior_color',
            'leads' => 'number_of_leads',
            'status' => 'status',
            'bodytype' => 'body_type',
            'drivetrain' => 'drive_train',
            'engine' => 'engine',
            'fueltype' => 'fuel_type',
            'isfeatured' => 'is_featured',
            'hasvideo' => 'has_video',
            'numberofpics' => 'number_of_pics',
            'images' => 'image_urls',
            'additionalimages' => 'additional_image_urls',
            'purchasedfrom' => 'purchased_from',
            'purchasedate' => 'purchase_date',
            'transmission' => 'transmission',
            'transmissiontype' => 'transmission_type',
            'vehiclepurchasesource' => 'vehicle_purchase_source',
            'advertisingprice' => 'advertising_price',
            'dealstatus' => 'deal_status',
            'solddate' => 'sold_date',
            'buyername' => 'buyer_name',
        ];
        
        foreach ($mappings as $csvField => $dbField) {
            if (isset($data[$csvField])) {
                $mapped[$dbField] = $this->formatFieldValue($dbField, $data[$csvField]);
            }
        }
        
        // Handle boolean fields
        if (isset($data['isfeatured'])) {
            $mapped['is_featured'] = $this->parseBoolean($data['isfeatured']);
        }
        
        if (isset($data['hasvideo'])) {
            $mapped['has_video'] = $this->parseBoolean($data['hasvideo']);
        }
        
        // Process the images
        $this->processImagesData($mapped);
        
        return $mapped;
    }
    
    /**
     * Process image data from the mapped fields
     *
     * @param array &$mapped The mapped vehicle data
     * @return void
     */
    protected function processImagesData(array &$mapped): void
    {
        $stockNumber = $mapped['stock_number'] ?? 'unknown';
        
        // Initialize additional_images array 
        $mapped['additional_images'] = [];
        
        // Process main image and additional images from the image_urls field
        if (isset($mapped['image_urls']) && !empty($mapped['image_urls'])) {
            // Check for different delimiters, prioritizing comma and space
            $urls = [];
            $delimiters = [',', ' ', '|', ';'];
            
            foreach ($delimiters as $delimiter) {
                if (strpos($mapped['image_urls'], $delimiter) !== false) {
                    $urls = array_map('trim', explode($delimiter, $mapped['image_urls']));
                    // Filter out empty values
                    $urls = array_filter($urls, fn($url) => !empty($url));
                    if (count($urls) > 1) {
                        $this->log('info', "Split image URLs using delimiter: '{$delimiter}'");
                        break;
                    }
                }
            }
            
            // If no delimiter found or no valid URLs after splitting, treat as single URL
            if (empty($urls)) {
                $urls = [trim($mapped['image_urls'])];
            }
            
            // Use the first image as the main image in vehicles table
            if (!empty($urls[0])) {
                $mapped['image_path'] = $this->cleanImageUrl($urls[0]);
                $this->log('info', "Stored main image URL for vehicle: {$stockNumber}");
                
                // Store additional images (if any) for the vehicle_images table
                for ($i = 1; $i < count($urls); $i++) {
                    if (!empty($urls[$i])) {
                        $mapped['additional_images'][] = $this->cleanImageUrl($urls[$i]);
                    }
                }
                
                if (count($urls) > 1) {
                    $this->log('info', "Found " . (count($urls) - 1) . " additional images in image_urls field for vehicle: {$stockNumber}");
                }
            } else {
                $mapped['image_path'] = null;
            }
        } else {
            $mapped['image_path'] = null;
        }
        
        // Process additional images field (if it exists)
        if (isset($mapped['additional_image_urls']) && !empty($mapped['additional_image_urls'])) {
            // Parse additional images field, prioritizing comma and space
            $additionalUrls = [];
            $delimiters = [',', ' ', '|', ';'];
            
            foreach ($delimiters as $delimiter) {
                if (strpos($mapped['additional_image_urls'], $delimiter) !== false) {
                    $additionalUrls = array_map('trim', explode($delimiter, $mapped['additional_image_urls']));
                    // Filter out empty values
                    $additionalUrls = array_filter($additionalUrls, fn($url) => !empty($url));
                    if (count($additionalUrls) > 1) {
                        $this->log('info', "Split additional image URLs using delimiter: '{$delimiter}'");
                        break;
                    }
                }
            }
            
            // If no delimiter found or no valid URLs after splitting, treat as single URL
            if (empty($additionalUrls)) {
                $additionalUrls = [trim($mapped['additional_image_urls'])];
            }
            
            // Add these images to the additional_images array
            foreach ($additionalUrls as $url) {
                if (!empty($url)) {
                    $mapped['additional_images'][] = $this->cleanImageUrl($url);
                }
            }
            
            if (!empty($additionalUrls)) {
                $this->log('info', "Found " . count($additionalUrls) . " images in additional_image_urls field for vehicle: {$stockNumber}");
            }
        }
        
        // Clean up temporary fields
        unset($mapped['image_urls']);
        unset($mapped['additional_image_urls']);
        
        // Log the total count of additional images
        if (!empty($mapped['additional_images'])) {
            $this->log('info', "Total of " . count($mapped['additional_images']) . " additional images will be stored in vehicle_images table for: {$stockNumber}");
        }
    }
    
    /**
     * Clean and format an image URL
     *
     * @param string $url Raw image URL
     * @return string Cleaned URL
     */
    protected function cleanImageUrl(string $url): string
    {
        // Basic URL cleaning
        $url = trim($url);
        $url = str_replace(' ', '%20', $url);
        $url = trim($url, '"\'');
        $url = str_replace('\\', '/', $url);
        
        // Add http:// if missing from URL
        if (strpos($url, '//') === 0) {
            $url = 'http:' . $url;
        } elseif (strpos($url, 'http') !== 0 && !empty($url)) {
            $url = 'http://' . $url;
        }
        
        return $url;
    }
    
    /**
     * Format field values based on their type
     *
     * @param string $field Field name
     * @param mixed $value Field value
     * @return mixed Formatted value
     */
    protected function formatFieldValue(string $field, $value)
    {
        // Handle date fields
        if (in_array($field, ['date_in_stock', 'purchase_date', 'sold_date'])) {
            if (empty(trim($value))) {
                return null; // Return null for empty dates
            }
            return date('Y-m-d', strtotime($value));
        }
        
        // Handle numeric fields
        if (in_array($field, ['year', 'odometer', 'number_of_leads', 'number_of_pics'])) {
            return !empty($value) ? (int) $value : null;
        }
        
        // Handle price field
        if ($field === 'advertising_price') {
            // Remove currency symbols and commas
            $value = preg_replace('/[^0-9.]/', '', $value);
            return !empty($value) ? (float) $value : null;
        }
        
        // For text fields, trim and set to null if empty
        $value = trim($value);
        return $value === '' ? null : $value;
    }
    
    /**
     * Parse boolean values from various formats
     * 
     * @param mixed $value The value to parse
     * @return bool The boolean value
     */
    protected function parseBoolean($value): bool
    {
        if (is_bool($value)) {
            return $value;
        }
        
        $trueValues = ['1', 'yes', 'true', 'y', 'on'];
        
        return in_array(strtolower((string) $value), $trueValues);
    }
    
    /**
     * Normalize CSV header names
     *
     * @param string $header Original header name
     * @return string Normalized header name
     */
    protected function normalizeHeader(string $header): string
    {
        // Remove special characters, convert to lowercase
        $normalized = preg_replace('/[^a-zA-Z0-9]/', '', $header);
        return strtolower($normalized);
    }
    
    /**
     * Send notifications for a single vehicle
     * This method is kept for backward compatibility but no longer called for individual vehicles
     *
     * @param Vehicle $vehicle
     * @param string $fileName
     * @return void
     */
    protected function sendNotifications(Vehicle $vehicle, string $fileName): void
    {
        // Get all users with vehicle notification permissions
        $notifiableUsers = User::permission('receive_vehicle_notifications')->get();
        
        if ($notifiableUsers->isEmpty()) {
            $this->log('info', "No users to notify for vehicle {$vehicle->stock_number}");
            return;
        }
        
        $notificationData = [
            'vehicle_id' => $vehicle->id,
            'stock_number' => $vehicle->stock_number,
            'vin' => $vehicle->vin,
            'year' => $vehicle->year,
            'make' => $vehicle->make,
            'model' => $vehicle->model,
            'price' => $vehicle->advertising_price,
            'image_url' => $vehicle->image_path,
            'import_source' => $fileName,
        ];
        
        // Send to each user with appropriate permissions
        Notification::send($notifiableUsers, new NewVehicleImported($notificationData));
        
        $this->log('info', "Sent notifications to " . $notifiableUsers->count() . " users for vehicle {$vehicle->stock_number}");
    }
    
    /**
     * Process additional images for a vehicle
     *
     * @param \App\Models\Vehicle $vehicle The vehicle to add images to
     * @param array $images Array of image URLs
     * @return void
     */
    protected function processAdditionalImages(\App\Models\Vehicle $vehicle, array $images): void
    {
        if (empty($images)) {
            return;
        }
        
        // Remove any existing images first (for updates)
        $vehicle->images()->delete();
        
        // Add new images
        foreach ($images as $index => $imageUrl) {
            // Only set the first image as featured if the vehicle doesn't have a main image
            $is_featured = ($index === 0 && empty($vehicle->image_path));
            
            $vehicle->images()->create([
                'image_url' => $imageUrl,
                'sort_order' => $index + 1,
                'is_featured' => $is_featured,
            ]);
        }
        
        $this->log('info', "Added " . count($images) . " additional images for vehicle: {$vehicle->stock_number}");
    }
    
   
} 