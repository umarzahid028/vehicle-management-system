<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\RepairImage;
use App\Policies\RepairImagePolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
        RepairImage::class => RepairImagePolicy::class,
    ];

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Implicitly grant "Admin" role all permissions
        Gate::before(function ($user, $ability) {
            if ($user->hasRole('Admin')) {
                return true;
            }
            return null;
        });

        // Define permissions for user management
        Gate::define('edit users', function ($user) {
            return $user->hasPermissionTo('edit users');
        });

        // Define gate for approving vendor estimates
        Gate::define('approve-estimates', function ($user) {
            return $user->hasAnyRole(['Admin', 'Sales Manager', 'Recon Manager']);
        });
    }
}
