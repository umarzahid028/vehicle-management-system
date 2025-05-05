<?php

namespace App\Observers;

use App\Models\SalesTeam;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Notifications\LoginCredentials;
use Illuminate\Support\Facades\DB;

class SalesTeamObserver
{
    /**
     * Handle the SalesTeam "created" event.
     */
    public function created(SalesTeam $salesTeam): void
    {
        // User creation is now handled in the controller
    }

    /**
     * Handle the SalesTeam "updated" event.
     */
    public function updated(SalesTeam $salesTeam): void
    {
        // Find the associated user
        $user = User::where('email', $salesTeam->getOriginal('email'))->first();
        
        if (!$user) {
            return;
        }

        $updates = [];

        if ($salesTeam->wasChanged('email')) {
            $updates['email'] = $salesTeam->email;
        }

        if ($salesTeam->wasChanged('name')) {
            $updates['name'] = $salesTeam->name;
        }

        if ($salesTeam->wasChanged('password')) {
            $updates['password'] = $salesTeam->password;
        }

        if ($salesTeam->wasChanged('phone')) {
            $updates['phone'] = $salesTeam->phone;
        }

        if (!empty($updates)) {
            $user->update($updates);
        }
    }

    /**
     * Handle the SalesTeam "deleted" event.
     */
    public function deleted(SalesTeam $salesTeam): void
    {
        // Optionally delete the associated user account
        // Uncomment if you want to delete the user when sales team member is deleted
        // User::where('email', $salesTeam->email)->delete();
    }

    /**
     * Handle the SalesTeam "restored" event.
     */
    public function restored(SalesTeam $salesTeam): void
    {
        //
    }

    /**
     * Handle the SalesTeam "force deleted" event.
     */
    public function forceDeleted(SalesTeam $salesTeam): void
    {
        // Delete the associated user account when force deleted
        User::where('email', $salesTeam->email)->delete();
    }
}
