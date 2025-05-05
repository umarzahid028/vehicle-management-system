<?php

namespace App\Services;

use App\Models\Vehicle;
use Illuminate\Support\Facades\Log;

class VehicleStatusService
{
    /**
     * Get all available statuses for vehicles
     * 
     * @return array
     */
    public function getAllStatuses(): array
    {
        return [
            // Available status
            Vehicle::STATUS_AVAILABLE,
            
            // Transport statuses
            Vehicle::STATUS_TRANSPORT_PENDING,
            Vehicle::STATUS_TRANSPORT_IN_TRANSIT,
            Vehicle::STATUS_TRANSPORT_IN_PROGRESS,
            Vehicle::STATUS_TRANSPORT_DELIVERED,
            Vehicle::STATUS_TRANSPORT_COMPLETED,
            Vehicle::STATUS_TRANSPORT_CANCELLED,
            
            // Inspection statuses
            Vehicle::STATUS_INSPECTION_PENDING,
            Vehicle::STATUS_INSPECTION_IN_PROGRESS,
            Vehicle::STATUS_INSPECTION_COMPLETED,
            Vehicle::STATUS_INSPECTION_CANCELLED,
            
            // Repair statuses
            Vehicle::STATUS_REPAIR_PENDING,
            Vehicle::STATUS_REPAIR_IN_PROGRESS,
            Vehicle::STATUS_REPAIR_COMPLETED,
            Vehicle::STATUS_REPAIR_CANCELLED,
            
            // Sales statuses
            Vehicle::STATUS_READY_FOR_SALE,
            Vehicle::STATUS_READY_FOR_SALE_ASSIGNED,
            Vehicle::STATUS_SOLD,
            
            // Goodwill claims statuses
            Vehicle::STATUS_GOODWILL_CLAIMS,
            Vehicle::STATUS_GOODWILL_CLAIMS_ASSIGNED,
            Vehicle::STATUS_GOODWILL_CLAIMS_COMPLETED,
            
            // Archive status
            Vehicle::STATUS_ARCHIVE
        ];
    }
    
    /**
     * Get available status transitions for the current status
     * 
     * @param string $currentStatus
     * @return array
     */
    public function getAvailableTransitions(string $currentStatus): array
    {
        $transitions = [
            // From Available
            Vehicle::STATUS_AVAILABLE => [
                Vehicle::STATUS_TRANSPORT_PENDING,
                Vehicle::STATUS_INSPECTION_PENDING,
                Vehicle::STATUS_REPAIR_PENDING,
                Vehicle::STATUS_READY_FOR_SALE,
                Vehicle::STATUS_GOODWILL_CLAIMS,
                Vehicle::STATUS_ARCHIVE
            ],
            
            // Transport workflow
            Vehicle::STATUS_TRANSPORT_PENDING => [
                Vehicle::STATUS_TRANSPORT_IN_PROGRESS,
                Vehicle::STATUS_TRANSPORT_IN_TRANSIT,
                Vehicle::STATUS_TRANSPORT_CANCELLED
            ],
            Vehicle::STATUS_TRANSPORT_IN_TRANSIT => [
                Vehicle::STATUS_TRANSPORT_DELIVERED,
                Vehicle::STATUS_TRANSPORT_CANCELLED
            ],
            Vehicle::STATUS_TRANSPORT_IN_PROGRESS => [
                Vehicle::STATUS_TRANSPORT_DELIVERED,
                Vehicle::STATUS_TRANSPORT_CANCELLED
            ],
            Vehicle::STATUS_TRANSPORT_DELIVERED => [
                Vehicle::STATUS_TRANSPORT_COMPLETED,
                Vehicle::STATUS_AVAILABLE,
                Vehicle::STATUS_INSPECTION_PENDING
            ],
            Vehicle::STATUS_TRANSPORT_COMPLETED => [
                Vehicle::STATUS_AVAILABLE,
                Vehicle::STATUS_INSPECTION_PENDING
            ],
            Vehicle::STATUS_TRANSPORT_CANCELLED => [
                Vehicle::STATUS_AVAILABLE
            ],
            
            // Inspection workflow
            Vehicle::STATUS_INSPECTION_PENDING => [
                Vehicle::STATUS_INSPECTION_IN_PROGRESS,
                Vehicle::STATUS_INSPECTION_CANCELLED
            ],
            Vehicle::STATUS_INSPECTION_IN_PROGRESS => [
                Vehicle::STATUS_INSPECTION_COMPLETED,
                Vehicle::STATUS_INSPECTION_CANCELLED
            ],
            Vehicle::STATUS_INSPECTION_COMPLETED => [
                Vehicle::STATUS_AVAILABLE,
                Vehicle::STATUS_REPAIR_PENDING,
                Vehicle::STATUS_READY_FOR_SALE
            ],
            Vehicle::STATUS_INSPECTION_CANCELLED => [
                Vehicle::STATUS_AVAILABLE
            ],
            
            // Repair workflow
            Vehicle::STATUS_REPAIR_PENDING => [
                Vehicle::STATUS_REPAIR_IN_PROGRESS,
                Vehicle::STATUS_REPAIR_CANCELLED
            ],
            Vehicle::STATUS_REPAIR_IN_PROGRESS => [
                Vehicle::STATUS_REPAIR_COMPLETED,
                Vehicle::STATUS_REPAIR_CANCELLED
            ],
            Vehicle::STATUS_REPAIR_COMPLETED => [
                Vehicle::STATUS_AVAILABLE,
                Vehicle::STATUS_READY_FOR_SALE
            ],
            Vehicle::STATUS_REPAIR_CANCELLED => [
                Vehicle::STATUS_AVAILABLE
            ],
            
            // Sales workflow
            Vehicle::STATUS_READY_FOR_SALE => [
                Vehicle::STATUS_READY_FOR_SALE_ASSIGNED,
                Vehicle::STATUS_AVAILABLE
            ],
            Vehicle::STATUS_READY_FOR_SALE_ASSIGNED => [
                Vehicle::STATUS_SOLD,
                Vehicle::STATUS_READY_FOR_SALE,
                Vehicle::STATUS_AVAILABLE
            ],
            Vehicle::STATUS_SOLD => [
                Vehicle::STATUS_ARCHIVE
            ],
            
            // Goodwill claims workflow
            Vehicle::STATUS_GOODWILL_CLAIMS => [
                Vehicle::STATUS_GOODWILL_CLAIMS_ASSIGNED,
                Vehicle::STATUS_AVAILABLE
            ],
            Vehicle::STATUS_GOODWILL_CLAIMS_ASSIGNED => [
                Vehicle::STATUS_GOODWILL_CLAIMS_COMPLETED,
                Vehicle::STATUS_GOODWILL_CLAIMS
            ],
            Vehicle::STATUS_GOODWILL_CLAIMS_COMPLETED => [
                Vehicle::STATUS_AVAILABLE,
                Vehicle::STATUS_READY_FOR_SALE
            ],
            
            // Archive can transition back to available
            Vehicle::STATUS_ARCHIVE => [
                Vehicle::STATUS_AVAILABLE
            ]
        ];
        
        return $transitions[$currentStatus] ?? [];
    }
    
    /**
     * Check if status transition is valid
     * 
     * @param string $fromStatus
     * @param string $toStatus
     * @return bool
     */
    public function isValidTransition(string $fromStatus, string $toStatus): bool
    {
        $allowedTransitions = $this->getAvailableTransitions($fromStatus);
        return in_array($toStatus, $allowedTransitions);
    }
    
    /**
     * Update vehicle status with validation
     * 
     * @param Vehicle $vehicle
     * @param string $newStatus
     * @param array $additionalData Additional data to update
     * @return bool
     */
    public function updateStatus(Vehicle $vehicle, string $newStatus, array $additionalData = []): bool
    {
        if (!$this->isValidTransition($vehicle->status, $newStatus)) {
            Log::warning("Invalid status transition attempted: {$vehicle->status} -> {$newStatus} for Vehicle ID: {$vehicle->id}");
            return false;
        }
        
        $updateData = array_merge(['status' => $newStatus], $additionalData);
        return $vehicle->update($updateData);
    }
    
    /**
     * Get all vehicles by status
     * 
     * @param string $status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getVehiclesByStatus(string $status)
    {
        return Vehicle::where('status', $status)->get();
    }
    
    /**
     * Get status category for a given status
     * 
     * @param string $status
     * @return string|null
     */
    public function getStatusCategory(string $status): ?string
    {
        $categories = [
            'available' => [Vehicle::STATUS_AVAILABLE],
            'transport' => [
                Vehicle::STATUS_TRANSPORT_PENDING,
                Vehicle::STATUS_TRANSPORT_IN_TRANSIT,
                Vehicle::STATUS_TRANSPORT_IN_PROGRESS,
                Vehicle::STATUS_TRANSPORT_DELIVERED,
                Vehicle::STATUS_TRANSPORT_COMPLETED,
                Vehicle::STATUS_TRANSPORT_CANCELLED
            ],
            'inspection' => [
                Vehicle::STATUS_INSPECTION_PENDING,
                Vehicle::STATUS_INSPECTION_IN_PROGRESS,
                Vehicle::STATUS_INSPECTION_COMPLETED,
                Vehicle::STATUS_INSPECTION_CANCELLED
            ],
            'repair' => [
                Vehicle::STATUS_REPAIR_PENDING,
                Vehicle::STATUS_REPAIR_IN_PROGRESS,
                Vehicle::STATUS_REPAIR_COMPLETED,
                Vehicle::STATUS_REPAIR_CANCELLED
            ],
            'sales' => [
                Vehicle::STATUS_READY_FOR_SALE,
                Vehicle::STATUS_READY_FOR_SALE_ASSIGNED,
                Vehicle::STATUS_SOLD
            ],
            'goodwill_claims' => [
                Vehicle::STATUS_GOODWILL_CLAIMS,
                Vehicle::STATUS_GOODWILL_CLAIMS_ASSIGNED,
                Vehicle::STATUS_GOODWILL_CLAIMS_COMPLETED
            ],
            'archive' => [Vehicle::STATUS_ARCHIVE]
        ];
        
        foreach ($categories as $category => $statuses) {
            if (in_array($status, $statuses)) {
                return $category;
            }
        }
        
        return null;
    }
} 