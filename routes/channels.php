<?php

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

// Log when channels file is loaded
Log::info('Broadcasting channels file loaded');

// Private user notifications channel
Broadcast::channel('users.{id}', function ($user, $id) {
    Log::debug("Authorizing user channel: users.{$id} for user {$user->id}");
    return (int) $user->id === (int) $id;
});

// Private channel for users with admin role
Broadcast::channel('admin', function ($user) {
    Log::debug("Authorizing admin channel for user {$user->id}");
    return $user->hasRole('Admin');
});

// Public vehicle imports channel - no auth required
Broadcast::channel('vehicles-imported', function () {
    Log::debug("Authorizing public vehicles-imported channel");
    return true;
}); 