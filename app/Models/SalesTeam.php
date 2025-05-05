<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class SalesTeam extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles;

    /**
     * The guard name for roles and permissions.
     */
    protected $guard_name = 'web';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'position',
        'bio',
        'photo_path',
        'is_active',
        'manager_id'
    ];

    protected $hidden = [
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Get the user account associated with this sales team member.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

    /**
     * Get the manager of the sales team member.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get the URL for the team member's photo.
     */
    public function getPhotoUrlAttribute()
    {
        return $this->photo_path ? Storage::url($this->photo_path) : null;
    }

    /**
     * Scope a query to only include active team members.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
