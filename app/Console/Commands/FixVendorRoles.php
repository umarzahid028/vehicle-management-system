<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Vendor;
use App\Enums\Role;

class FixVendorRoles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fix-vendor-roles {email? : Optional specific email to fix}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix vendor roles for users with vendor accounts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        if ($email) {
            // Fix specific user
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $this->error("User with email {$email} not found.");
                return 1;
            }
            
            $vendor = Vendor::where('email', $email)->first();
            
            if (!$vendor) {
                $this->error("Vendor with email {$email} not found.");
                return 1;
            }
            
            $this->fixVendorRole($user, $vendor);
            $this->info("Fixed role for user {$email}");
            return 0;
        }
        
        // Fix all vendors
        $vendors = Vendor::all();
        $count = 0;
        
        foreach ($vendors as $vendor) {
            $user = User::where('email', $vendor->email)->first();
            
            if ($user) {
                $this->fixVendorRole($user, $vendor);
                $count++;
            }
        }
        
        $this->info("Fixed roles for {$count} vendor users");
        return 0;
    }
    
    /**
     * Fix the role for a vendor user
     */
    private function fixVendorRole(User $user, Vendor $vendor)
    {
        // Ensure user has Vendor role assigned
        if (!$user->hasRole('Vendor')) {
            $user->assignRole('Vendor');
        }
        
        // Set the appropriate enum role based on vendor type
        if ($vendor->type) {
            $user->role = $vendor->type->is_on_site ? 
                Role::ONSITE_VENDOR : 
                Role::OFFSITE_VENDOR;
        } else {
            // Default to off-site if no vendor type is set
            $user->role = Role::OFFSITE_VENDOR;
        }
        
        $user->save();
    }
} 