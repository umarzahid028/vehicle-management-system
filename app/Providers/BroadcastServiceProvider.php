<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Log::info('BroadcastServiceProvider boot method called');

        // Check if broadcasting routes are enabled
        if ($this->app->environment('local', 'staging', 'production')) {
            Log::info('Broadcasting routes enabled');
            
            Broadcast::routes(['middleware' => ['web', 'auth']]);

            require base_path('routes/channels.php');
        } else {
            Log::warning('Broadcasting routes not enabled for current environment: ' . $this->app->environment());
        }
    }
} 