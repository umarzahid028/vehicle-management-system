<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laravel\Sanctum\HasApiTokens;
use App\Enums\Role;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'vendor_type',
        'is_active',
        'transporter_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => Role::class,
        'is_active' => 'boolean',
    ];

    public function transporter()
    {
        return $this->belongsTo(Transporter::class);
    }

    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'email', 'email');
    }

    public function salesTeam()
    {
        return $this->hasOne(SalesTeam::class, 'email', 'email');
    }

    public function receivesBroadcastNotificationsOn(): string
    {
        return 'users.' . $this->id;
    }

    public function inspectionAssignments(): HasMany
    {
        return $this->hasMany(InspectionAssignment::class, 'vendor_id');
    }

    public function assignedInspections(): HasMany
    {
        return $this->hasMany(VehicleInspection::class, 'vendor_id');
    }

    public function canEnterCosts(): bool
    {
        return $this->role?->canEnterCosts() ?? false;
    }

    public function canApproveEstimates(): bool
    {
        return $this->role?->canApproveEstimates() ?? false;
    }

    public function isVendor(): bool
    {
        return $this->role?->isVendor() ?? false;
    }

    public function isOnSiteVendor(): bool
    {
        return $this->role === Role::ONSITE_VENDOR;
    }

    public function isOffSiteVendor(): bool
    {
        return $this->role === Role::OFFSITE_VENDOR;
    }

    /**
     * Check if the user has system access.
     * For vendors, this depends on their vendor type settings.
     */
    public function hasSystemAccess(): bool
    {
        // Non-vendor users automatically have access
        if (!$this->isVendor()) {
            return true;
        }
        
        // For vendors, check the vendor type's has_system_access setting
        return $this->vendor && $this->vendor->type && $this->vendor->type->has_system_access;
    }

    public function vehicleReads(): HasMany
    {
        return $this->hasMany(VehicleRead::class);
    }
    
    /**
     * Check if user has read the specified vehicle.
     */
    public function hasReadVehicle($vehicleId): bool
    {
        return $this->vehicleReads()
            ->where('vehicle_id', $vehicleId)
            ->exists();
    }
}
