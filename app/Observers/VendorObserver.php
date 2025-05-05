<?php

namespace App\Observers;

use App\Models\Vendor;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Notifications\LoginCredentials;

class VendorObserver
{
    /**
     * Handle the Vendor "created" event.
     */
    public function created(Vendor $vendor): void
    {
        // Skip user creation if the user already exists
        $user = User::where('email', $vendor->email)->first();
        
        if ($user) {
            // Update the existing user with vendor role
            $user->assignRole('Vendor');
            
            // Set the appropriate role enum value based on vendor type
            if ($vendor->type) {
                $user->role = $vendor->type->is_on_site ? 
                    \App\Enums\Role::ONSITE_VENDOR : 
                    \App\Enums\Role::OFFSITE_VENDOR;
                $user->save();
            }
            
            return;
        }

        // Generate a random password
        $password = Str::random(10);
        
        // Create user account
        $user = User::create([
            'name' => $vendor->contact_person ?? $vendor->name,
            'email' => $vendor->email,
            'password' => Hash::make($password),
            'role' => $vendor->type && $vendor->type->is_on_site ? 
                \App\Enums\Role::ONSITE_VENDOR : 
                \App\Enums\Role::OFFSITE_VENDOR,
        ]);

        // Assign vendor role
        $user->assignRole('Vendor');

        // Send login credentials notification
        $user->notify(new LoginCredentials($password, 'Vendor'));

        // Remove the session flash since we're handling it in the controller
        session()->forget('generated_password');
    }

    /**
     * Handle the Vendor "updated" event.
     */
    public function updated(Vendor $vendor): void
    {
        if ($vendor->wasChanged('email')) {
            // Update associated user email
            User::where('email', $vendor->getOriginal('email'))
                ->update(['email' => $vendor->email]);
        }

        if ($vendor->wasChanged('name') || $vendor->wasChanged('contact_person')) {
            // Update associated user name
            User::where('email', $vendor->email)
                ->update(['name' => $vendor->contact_person ?? $vendor->name]);
        }
    }

    /**
     * Handle the Vendor "deleted" event.
     */
    public function deleted(Vendor $vendor): void
    {
        // Optionally delete the associated user account
        // Uncomment if you want to delete the user when vendor is deleted
        // User::where('email', $vendor->email)->delete();
    }
} 