<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewVehiclesImported implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The data to broadcast
     *
     * @var array
     */
    public $data;

    /**
     * Create a new event instance.
     */
    public function __construct(array $data)
    {
        // Transform vehicle objects to simple arrays to avoid serialization issues
        if (isset($data['new_vehicles'])) {
            $data['new_vehicles'] = array_map(function($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'stock_number' => $vehicle->stock_number,
                    'year' => $vehicle->year,
                    'make' => $vehicle->make,
                    'model' => $vehicle->model,
                    'vin' => $vehicle->vin,
                    'image_url' => $vehicle->image_url,
                    'status' => $vehicle->status,
                ];
            }, $data['new_vehicles']);
        }
        
        if (isset($data['modified_vehicles'])) {
            $data['modified_vehicles'] = array_map(function($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'stock_number' => $vehicle->stock_number,
                    'year' => $vehicle->year,
                    'make' => $vehicle->make,
                    'model' => $vehicle->model,
                    'vin' => $vehicle->vin,
                    'image_url' => $vehicle->image_url,
                    'status' => $vehicle->status,
                ];
            }, $data['modified_vehicles']);
        }
        
        $this->data = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('vehicles-imported'),
        ];
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith(): array
    {
        return $this->data;
    }
    
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'vehicles.imported';
    }
} 