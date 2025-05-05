<?php

namespace App\Notifications;

use App\Models\GoodwillClaim;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewGoodwillClaimSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The goodwill claim instance.
     *
     * @var \App\Models\GoodwillClaim
     */
    protected $claim;

    /**
     * Create a new notification instance.
     */
    public function __construct(GoodwillClaim $claim)
    {
        $this->claim = $claim;
    }

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
        $url = route('sales.goodwill-claims.show', $this->claim);

        return (new MailMessage)
            ->subject('New Goodwill Claim Submitted')
            ->line('A new goodwill claim has been submitted for review.')
            ->line("Customer: {$this->claim->customer_name}")
            ->line("Vehicle: {$this->claim->vehicle->stock_number}")
            ->line("Estimated Cost: $" . number_format($this->claim->estimated_cost, 2))
            ->action('View Claim', $url)
            ->line('Please review this claim at your earliest convenience.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'claim_id' => $this->claim->id,
            'vehicle_id' => $this->claim->vehicle_id,
            'customer_name' => $this->claim->customer_name,
            'estimated_cost' => $this->claim->estimated_cost,
            'submitted_by' => $this->claim->createdBy->name,
        ];
    }
} 