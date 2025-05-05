<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'is_on_site',
        'has_system_access',
        'is_active'
    ];

    protected $casts = [
        'is_on_site' => 'boolean',
        'has_system_access' => 'boolean',
        'is_active' => 'boolean',
    ];
    
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        // Ensure on-site vendors always have system access
        static::saving(function ($vendorType) {
            if ($vendorType->is_on_site) {
                $vendorType->has_system_access = true;
            }
        });
        
        // When vendor type is updated, update all associated vendor users
        static::updated(function ($vendorType) {
            if ($vendorType->isDirty('is_on_site') || $vendorType->isDirty('has_system_access')) {
                // Update all vendors of this type
                $vendorType->vendors->each(function ($vendor) use ($vendorType) {
                    // Update vendor user if exists
                    if ($vendor->user) {
                        // Update role based on site type
                        $isOnSite = $vendorType->is_on_site;
                        $hasSystemAccess = $vendorType->has_system_access;
                        
                        $vendor->user()->update([
                            'role' => $isOnSite ? \App\Enums\Role::ONSITE_VENDOR : \App\Enums\Role::OFFSITE_VENDOR,
                            'is_active' => $vendor->is_active && $hasSystemAccess,
                        ]);
                    }
                });
            }
        });
    }

    public function vendors()
    {
        return $this->hasMany(Vendor::class);
    }
}
