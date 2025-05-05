<?php

namespace App\Notifications;

use App\Models\SalesIssue;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSalesIssueReported extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The sales issue instance.
     *
     * @var \App\Models\SalesIssue
     */
    protected $salesIssue;

    /**
     * Create a new notification instance.
     */
    public function __construct(SalesIssue $salesIssue)
    {
        $this->salesIssue = $salesIssue;
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
        $url = route('sales.issues.show', $this->salesIssue);
        
        return (new MailMessage)
            ->subject('New Sales Issue Reported')
            ->line('A new sales issue has been reported.')
            ->line("Vehicle: {$this->salesIssue->vehicle->stock_number} - {$this->salesIssue->vehicle->year} {$this->salesIssue->vehicle->make} {$this->salesIssue->vehicle->model}")
            ->line("Issue Type: {$this->salesIssue->issue_type}")
            ->line("Priority: {$this->salesIssue->priority}")
            ->line("Description: {$this->salesIssue->description}")
            ->action('View Issue', $url)
            ->line('Please review this issue as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'sales_issue_id' => $this->salesIssue->id,
            'vehicle_id' => $this->salesIssue->vehicle_id,
            'issue_type' => $this->salesIssue->issue_type,
            'priority' => $this->salesIssue->priority,
            'reported_by' => $this->salesIssue->reported_by_user_id,
        ];
    }
}
