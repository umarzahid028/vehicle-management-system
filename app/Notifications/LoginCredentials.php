<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoginCredentials extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected string $password,
        protected string $role
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your TrevinosAuto Portal Login Credentials')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('Your account has been created in the TrevinosAuto Portal.')
            ->line('You have been assigned the role of: ' . $this->role)
            ->line('Please use the following credentials to log in:')
            ->line('Email: ' . $notifiable->email)
            ->line('Password: ' . $this->password)
            ->action('Login Now', url('/login'))
            ->line('For security reasons, please change your password after your first login.')
            ->line('If you have any questions, please contact our support team.');
    }
} 