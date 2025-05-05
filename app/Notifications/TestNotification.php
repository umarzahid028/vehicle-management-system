<?php

namespace App\Notifications;

class TestNotification extends BaseNotification
{
    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        $this->message = "This is a test notification";
        $this->actionUrl = route('dashboard');
        $this->actionText = 'Go to Dashboard';
    }
} 