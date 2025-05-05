<?php

namespace App\Notifications;

use App\Models\Vehicle;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewVehicleImported extends Notification
{
    use Queueable;

    /**
     * The notification data.
     *
     * @var array
     */
    protected $data;

    /**
     * Create a new notification instance.
     * 
     * @param array|Vehicle $data Either a Vehicle object or an array of vehicle data
     * @param string|null $fileName Optional file name if $data is a Vehicle object
     */
    public function __construct($data, ?string $fileName = null)
    {
        // Handle backward compatibility with old constructor signature
        if ($data instanceof Vehicle) {
            $this->data = [
                'vehicle_id' => $data->id,
                'stock_number' => $data->stock_number,
                'vin' => $data->vin,
                'year' => $data->year,
                'make' => $data->make,
                'model' => $data->model,
                'price' => $data->advertising_price,
                'image_url' => $data->image_path,
                'import_source' => $fileName,
                'vehicle_info' => "{$data->year} {$data->make} {$data->model}",
            ];
        } else {
            $this->data = $data;
            
            // Ensure vehicle_info is set for convenience
            if (!isset($this->data['vehicle_info']) && isset($this->data['year']) && isset($this->data['make']) && isset($this->data['model'])) {
                $this->data['vehicle_info'] = "{$this->data['year']} {$this->data['make']} {$this->data['model']}";
            }
        }
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
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject("New Vehicle Imported")
            ->greeting("Hello {$notifiable->name}!");
            
        if (isset($this->data['vehicle_info'])) {
            $mail->subject("New Vehicle Imported - {$this->data['vehicle_info']}");
        }
        
        if (isset($this->data['import_source'])) {
            $mail->line("A new vehicle has been imported from the file: {$this->data['import_source']}");
        } else {
            $mail->line("A new vehicle has been imported.");
        }
        
        $mail->line("Vehicle Details:");
        
        if (isset($this->data['stock_number'])) {
            $mail->line("Stock #: {$this->data['stock_number']}");
        }
        
        if (isset($this->data['vin'])) {
            $mail->line("VIN: {$this->data['vin']}");
        }
        
        if (isset($this->data['vehicle_info'])) {
            $mail->line("Vehicle: {$this->data['vehicle_info']}");
        }
        
        if (isset($this->data['vehicle_id'])) {
            $mail->action('View Vehicle Details', url("/vehicles/{$this->data['vehicle_id']}"));
        }
        
        $mail->line('Thank you for using our application!');
        
        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return array_merge($this->data, [
            'message' => $this->data['message'] ?? "New vehicle imported",
            'type' => 'vehicle_imported'
        ]);
    }
}
