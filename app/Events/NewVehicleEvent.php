<?php

namespace App\Events;

use App\Models\Vehicle;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewVehicleEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The number of new vehicles.
     */
    public int $vehicleCount;

    /**
     * The list of new vehicle IDs
     */
    public array $vehicleIds;

    /**
     * The event type (new or update)
     */
    public string $eventType;

    /**
     * Create a new event instance.
     *
     * @param int $vehicleCount
     * @param array $vehicleIds
     * @param string $eventType
     */
    public function __construct(int $vehicleCount, array $vehicleIds = [], string $eventType = 'new')
    {
        $this->vehicleCount = $vehicleCount;
        $this->vehicleIds = $vehicleIds;
        $this->eventType = $eventType;
        
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('vehicles');
    }
    
    /**
     * The event's broadcast name.
     * 
     * @return string
     */
    public function broadcastAs()
    {
        return 'vehicle.created';
    }
    
    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'vehicleCount' => $this->vehicleCount,
            'vehicleIds' => $this->vehicleIds,
            'eventType' => $this->eventType,
            'timestamp' => now()->timestamp,
        ];
    }
} 