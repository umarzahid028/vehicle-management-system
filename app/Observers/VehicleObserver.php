<?php

namespace App\Observers;

use App\Models\Vehicle;
use App\Models\User;
use App\Notifications\VehicleReadyForSale;
use Illuminate\Support\Facades\Notification;

class VehicleObserver
{
    /**
     * Handle the Vehicle "updated" event.
     */
    public function updated(Vehicle $vehicle): void
    {
        // Check if status was changed to "Ready for Sale"
        if ($vehicle->wasChanged('status') && $vehicle->status === Vehicle::STATUS_READY_FOR_SALE) {
            // Notify all sales team members
            $salesTeamMembers = User::role('Sales Team')->get();
            Notification::send($salesTeamMembers, new VehicleReadyForSale($vehicle));
        }
    }
} 