<?php

namespace App\Notifications;

use App\Models\VendorEstimate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewEstimateRequiresApproval extends Notification implements ShouldQueue
{
    use Queueable;

    protected $estimate;

    /**
     * Create a new notification instance.
     */
    public function __construct(VendorEstimate $estimate)
    {
        $this->estimate = $estimate;
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
        $vehicle = $this->estimate->inspectionItemResult->vehicleInspection->vehicle;
        $item = $this->estimate->inspectionItemResult->inspectionItem;
        
        return (new MailMessage)
            ->subject('New Vendor Estimate Requires Approval')
            ->line('A new vendor estimate requires your approval.')
            ->line("Vehicle: {$vehicle->year} {$vehicle->make} {$vehicle->model} (Stock #: {$vehicle->stock_number})")
            ->line("Item: {$item->name}")
            ->line("Vendor: {$this->estimate->vendor->name}")
            ->line("Estimated Cost: $" . number_format($this->estimate->estimated_cost, 2))
            ->action('Review Estimate', url(route('vendor-estimates.pending')))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $vehicle = $this->estimate->inspectionItemResult->vehicleInspection->vehicle;
        
        return [
            'vendor_estimate_id' => $this->estimate->id,
            'message' => "New estimate from {$this->estimate->vendor->name} for {$vehicle->year} {$vehicle->make} {$vehicle->model}",
            'cost' => $this->estimate->estimated_cost,
            'vehicle_id' => $vehicle->id,
        ];
    }
}
