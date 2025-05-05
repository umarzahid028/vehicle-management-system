<?php

namespace App\Providers;

use App\Models\Transport;
use App\Models\Transporter;
use App\Models\Vendor;
use App\Observers\TransportObserver;
use App\Observers\TransporterObserver;
use App\Observers\VendorObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Event;
use App\Events\NewVehicleEvent;
use App\Events\NewVehiclesImported;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the role directive
        Blade::if('role', function ($role) {
            return auth()->check() && auth()->user()->hasRole($role);
        });

        // Register observers
        Transport::observe(TransportObserver::class);
        Transporter::observe(TransporterObserver::class);
        Vendor::observe(VendorObserver::class);

        // View composer for sidebar to count vehicles
        view()->composer('components.sidebar', function ($view) {
            $newVehicleCount = 0;
            
            // Only fetch count if user is logged in and has appropriate roles
            if (auth()->check() && auth()->user()->hasAnyRole(['Admin', 'Sales Manager', 'Recon Manager'])) {
                $newVehicleCount = \App\Models\Vehicle::where('created_at', '>=', now()->subDays(7))
                    ->whereDoesntHave('vehicleReads', function($query) {
                        $query->where('user_id', auth()->id());
                    })
                    ->count();
            }
            
            $view->with('newVehicleCount', $newVehicleCount);
        });

        // View composer for vehicles index to pass new vehicle count
        view()->composer('vehicles.index', function ($view) {
            if (auth()->check()) {
                $newVehicleCount = \App\Models\Vehicle::where('created_at', '>=', now()->subDays(7))
                    ->whereDoesntHave('vehicleReads', function($query) {
                        $query->where('user_id', auth()->id());
                    })
                    ->count();
                
                $view->with('newVehicleCount', $newVehicleCount);
            }
        });

        // Share transport counts with sidebar view
        View::composer('components.sidebar', function ($view) {
            $transportCount = 0;
            $transporterCount = 0;
            
            // Only query the database if user is authenticated
            if (Auth::check()) {
                $transportCount = \App\Models\Transport::whereIn('status', ['pending', 'in_transit', 'picked_up'])->count();
                
                // If user is a transporter, get their specific counts
                if (Auth::user()->hasRole('Transporter') && Auth::user()->transporter_id) {
                    $transporterCount = \App\Models\Transport::where('transporter_id', Auth::user()->transporter_id)
                        ->whereIn('status', ['pending', 'in_transit', 'picked_up'])
                        ->count();
                }
            }
            
            $view->with([
                'transportCount' => $transportCount,
                'transporterCount' => $transporterCount
            ]);
        });

        // Ensure broadcasting events are processed directly and not queued
        Event::listen(function (NewVehicleEvent $event) {
            Log::info('NewVehicleEvent captured in AppServiceProvider listener');
        });
        
        // Listen for NewVehiclesImported event
        Event::listen(function (NewVehiclesImported $event) {
            Log::info('NewVehiclesImported event captured in AppServiceProvider', [
                'new_count' => $event->data['new_count'] ?? 0,
                'modified_count' => $event->data['modified_count'] ?? 0
            ]);
        });

        // Set the default broadcast connection for all environments
        Broadcast::routes();
        
        // Configure Pusher to use sync driver for artisan commands
        if ($this->app->runningInConsole()) {
            Log::info('Running in console mode, configuring broadcasting');
            
            // Force immediate broadcast processing
            config(['queue.default' => 'sync']);
            
            // Add special case for running in a command context
            if (php_sapi_name() === 'cli') {
                Log::info('CLI detected, ensuring broadcaster is configured');
                
                // Make sure Pusher client has what it needs
                $pusherConfig = config('broadcasting.connections.pusher');
                if (!empty($pusherConfig)) {
                    // Log the Pusher configuration (without sensitive data)
                    Log::info('Pusher configured with:', [
                        'key' => !empty($pusherConfig['key']) ? 'set' : 'not set',
                        'cluster' => $pusherConfig['options']['cluster'] ?? 'not set',
                        'encrypted' => $pusherConfig['options']['encrypted'] ?? false,
                    ]);
                } else {
                    Log::warning('Pusher configuration not found, broadcasting may not work');
                }
            }
        }

        // Force sync queue for broadcasting
        if (app()->environment('local', 'staging', 'production')) {
            Queue::before(function (JobProcessing $event) {
                if (isset($event->job->payload()['data']['command']) && 
                    str_contains($event->job->payload()['data']['command'], 'BroadcastEvent')) {
                    Log::info('Broadcasting job detected, processing immediately', [
                        'job' => $event->job->getName(),
                        'connection' => $event->connectionName,
                        'payload' => $event->job->payload(),
                    ]);
                }
            });
        }
    }
}
