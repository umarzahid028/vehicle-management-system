<?php

namespace App\Notifications;

use App\Models\Vehicle;
use App\Models\VehicleInspection;

class InspectionCompleted extends BaseNotification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(VehicleInspection $inspection)
    {
        $this->message = "Inspection completed for vehicle {$inspection->vehicle->vin}";
        $this->actionUrl = route('inspection.inspections.show', $inspection);
        $this->actionText = 'View Inspection';
    }
} 