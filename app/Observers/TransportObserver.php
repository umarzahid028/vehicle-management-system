<?php

namespace App\Observers;

use App\Models\Transport;

class TransportObserver
{
    /**
     * Handle the Transport "updated" event.
     */
    public function updated(Transport $transport): void
    {
        // Check if the status was changed to "delivered"
        if ($transport->wasChanged('status') && $transport->status === 'delivered') {
            // Update the associated vehicle's transport_status
            $transport->vehicle->update([
                'transport_status' => 'delivered',
                'status' => 'arrived'
            ]);
        }
    }
} 