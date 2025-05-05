<?php

namespace App\Policies;

use App\Models\RepairImage;
use App\Models\User;

class RepairImagePolicy
{
    /**
     * Determine if the user can delete the repair image.
     */
    public function delete(User $user, RepairImage $repairImage): bool
    {
        // Allow admin users to delete any image
        if ($user->is_admin) {
            return true;
        }

        // Allow vendors to delete images for their assigned repairs
        if ($user->vendor) {
            return $repairImage->itemResult->vendor_id === $user->vendor->id;
        }

        return false;
    }
} 