<?php

namespace App\Observers;

use App\Models\Transporter;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Notifications\LoginCredentials;

class TransporterObserver
{
    /**
     * Handle the Transporter "created" event.
     */
    public function created(Transporter $transporter): void
    {
        // Skip user creation if the user already exists
        if (User::where('email', $transporter->email)->exists()) {
            return;
        }

        // Generate a random password
        $password = Str::random(10);
        
        // Create user account
        $user = User::create([
            'name' => $transporter->contact_person ?? $transporter->name,
            'email' => $transporter->email,
            'password' => Hash::make($password),
            'transporter_id' => $transporter->id,
        ]);

        // Assign transporter role
        $user->assignRole('Transporter');

        // Send login credentials notification
        $user->notify(new LoginCredentials($password, 'Transporter'));
    }

    /**
     * Handle the Transporter "updated" event.
     */
    public function updated(Transporter $transporter): void
    {
        if ($transporter->wasChanged('email')) {
            // Update associated user email
            User::where('email', $transporter->getOriginal('email'))
                ->update(['email' => $transporter->email]);
        }

        if ($transporter->wasChanged('name') || $transporter->wasChanged('contact_person')) {
            // Update associated user name
            User::where('email', $transporter->email)
                ->update(['name' => $transporter->contact_person ?? $transporter->name]);
        }
    }

    /**
     * Handle the Transporter "deleted" event.
     */
    public function deleted(Transporter $transporter): void
    {
        // Optionally delete the associated user account
        // Uncomment if you want to delete the user when transporter is deleted
        // User::where('email', $transporter->email)->delete();
    }
} 