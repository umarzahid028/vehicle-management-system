<?php

namespace App\Notifications;

use App\Models\Vehicle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NewVehicleArrival extends Notification
{
    use Queueable;

    /**
     * The vehicle instance.
     *
     * @var Vehicle
     */
    protected $vehicle;

    /**
     * Create a new notification instance.
     */
    public function __construct(Vehicle $vehicle)
    {
        $this->vehicle = $vehicle;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'vehicle_id' => $this->vehicle->id,
            'stock_number' => $this->vehicle->stock_number,
            'vin' => $this->vehicle->vin,
            'vehicle_info' => "{$this->vehicle->year} {$this->vehicle->make} {$this->vehicle->model}",
            'message' => "New vehicle has been added to inventory",
            'type' => 'vehicle_arrival'
        ];
    }

    public function toMail($notifiable)
    {
        $vehicleInfo = "{$this->vehicle->year} {$this->vehicle->make} {$this->vehicle->model}";
        
        return (new MailMessage)
            ->subject("New Vehicle Added - {$vehicleInfo}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("A new vehicle has been added to the system.")
            ->line("Vehicle Details:")
            ->line("Stock #: {$this->vehicle->stock_number}")
            ->line("VIN: {$this->vehicle->vin}")
            ->line("Vehicle: {$vehicleInfo}")
            ->action('View Vehicle Details', route('vehicles.show', $this->vehicle))
            ->line('Thank you for using our application!');
    }

    public function toDatabase($notifiable): array
    {
        return $this->toArray($notifiable);
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toArray($notifiable));
    }
} 