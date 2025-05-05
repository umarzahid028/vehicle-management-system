<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vendor;
use App\Models\User;
use App\Enums\Role;

class UpdateVendorAccess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendors:update-access';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates vendor user accounts to have correct system access based on vendor type';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating vendor access settings...');

        $vendors = Vendor::with(['type', 'user'])->get();
        $updated = 0;
        $noChanges = 0;
        $noUser = 0;

        foreach ($vendors as $vendor) {
            if (!$vendor->user) {
                $this->warn("Vendor '{$vendor->name}' does not have an associated user account.");
                $noUser++;
                continue;
            }

            $hasSystemAccess = $vendor->type && $vendor->type->has_system_access;
            $isOnSite = $vendor->type && $vendor->type->is_on_site;
            $currentActive = $vendor->user->is_active;
            $isVendorActive = $vendor->is_active;

            // Determine role and active status
            $newRole = $isOnSite ? Role::ONSITE_VENDOR : Role::OFFSITE_VENDOR;
            $newActiveStatus = $isVendorActive && $hasSystemAccess;

            // Check if update is needed
            if ($vendor->user->role !== $newRole || $currentActive !== $newActiveStatus) {
                $vendor->user->update([
                    'role' => $newRole,
                    'is_active' => $newActiveStatus,
                ]);
                
                $this->info("Updated user '{$vendor->user->name}' - Role: {$newRole->value}, Access: " . ($newActiveStatus ? 'Enabled' : 'Disabled'));
                $updated++;
            } else {
                $noChanges++;
            }
        }

        $this->info("Completed updating vendor access settings:");
        $this->info("- {$updated} vendor accounts updated");
        $this->info("- {$noChanges} vendor accounts already had correct settings");
        $this->info("- {$noUser} vendors without user accounts");

        return 0;
    }
} 