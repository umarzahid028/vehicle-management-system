<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'contact_person',
        'email',
        'phone',
        'address',
        'specialty_tags',
        'type_id',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'specialty_tags' => 'array',
    ];

    /**
     * Get the user account associated with this vendor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    /**
     * Get the vendor type that this vendor belongs to.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(VendorType::class, 'type_id');
    }

    /**
     * Check if this vendor is on-site.
     */
    public function isOnSite(): bool
    {
        return $this->type && $this->type->is_on_site;
    }

    /**
     * Get the vehicle inspections for this vendor.
     */
    public function vehicleInspections(): HasMany
    {
        return $this->hasMany(VehicleInspection::class);
    }

    /**
     * Get the inspection item results for this vendor.
     */
    public function inspectionItemResults(): HasMany
    {
        return $this->hasMany(InspectionItemResult::class, 'vendor_id');
    }

    /**
     * Get the vendor estimates submitted by this vendor.
     */
    public function vendorEstimates(): HasMany
    {
        return $this->hasMany(VendorEstimate::class);
    }
} 