<?php

namespace App\Notifications;

use App\Models\Batch;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewBatchAssigned extends Notification implements ShouldQueue
{
    use Queueable;

    protected $batch;

    /**
     * Create a new notification instance.
     */
    public function __construct(Batch $batch)
    {
        $this->batch = $batch;
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
            ->subject('New Batch Assigned')
            ->line('A new batch has been assigned to you.')
            ->line('Batch Name: ' . $this->batch->name)
            ->line('Total Vehicles: ' . $this->batch->vehicles()->count())
            ->line('Origin: ' . $this->batch->origin)
            ->line('Destination: ' . $this->batch->destination)
            ->action('View Batch Details', route('batches.show', $this->batch->id))
            ->line('Please review and acknowledge the batch assignment.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'New batch assigned: ' . $this->batch->name,
            'batch_id' => $this->batch->id,
            'batch_name' => $this->batch->name,
            'origin' => $this->batch->origin,
            'destination' => $this->batch->destination,
            'total_vehicles' => $this->batch->vehicles()->count(),
            'link' => route('batches.show', $this->batch->id)
        ];
    }
} 