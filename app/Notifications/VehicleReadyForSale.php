<?php

namespace App\Notifications;

use App\Models\Vehicle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VehicleReadyForSale extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Vehicle $vehicle
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Vehicle Ready for Sale')
            ->line('A new vehicle is ready for sale!')
            ->line("Vehicle: {$this->vehicle->year} {$this->vehicle->make->name} {$this->vehicle->model->name}")
            ->line("VIN: {$this->vehicle->vin}")
            ->action('View Vehicle', route('vehicles.show', $this->vehicle))
            ->line('You can view this vehicle on your dashboard.');
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
            'message' => "New vehicle ready for sale: {$this->vehicle->year} {$this->vehicle->make->name} {$this->vehicle->model->name}",
            'action_url' => route('vehicles.show', $this->vehicle),
        ];
    }
} 