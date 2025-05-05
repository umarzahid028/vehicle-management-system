<?php

namespace App\Notifications;

use App\Models\Transport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewTransportAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The transport instance.
     *
     * @var \App\Models\Transport
     */
    protected $transport;

    /**
     * Create a new notification instance.
     */
    public function __construct(Transport $transport)
    {
        $this->transport = $transport;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Transport Assignment')
            ->greeting('Hello ' . $notifiable->name)
            ->line('You have been assigned a new transport.')
            ->line("Vehicle: {$this->transport->vehicle->make} {$this->transport->vehicle->model}")
            ->line("From: {$this->transport->origin}")
            ->line("To: {$this->transport->destination}")
            ->line("Pickup Date: {$this->transport->pickup_date->format('M d, Y')}")
            ->action('View Transport Details', route('transports.show', $this->transport))
            ->line('Please review the transport details and acknowledge the assignment.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'transport_id' => $this->transport->id,
            'message' => 'New transport assignment',
            'vehicle' => "{$this->transport->vehicle->make} {$this->transport->vehicle->model}",
            'origin' => $this->transport->origin,
            'destination' => $this->transport->destination,
            'pickup_date' => $this->transport->pickup_date->format('M d, Y'),
            'status' => $this->transport->status,
            'link' => route('transports.show', $this->transport)
        ];
    }
}
