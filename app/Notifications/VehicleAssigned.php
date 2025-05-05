<?php

namespace App\Notifications;

use App\Models\Vehicle;

class VehicleAssigned extends BaseNotification
{
    /**
     * Create a new notification instance.
     */
    public function __construct(Vehicle $vehicle)
    {
        $this->message = "Vehicle {$vehicle->vin} has been assigned to you";
        $this->actionUrl = route('vehicles.show', $vehicle);
        $this->actionText = 'View Vehicle';
    }
} 