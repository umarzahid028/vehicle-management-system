<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class Transporter extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'contact_person',
        'phone',
        'email',
        'address',
        'city',
        'state',
        'zip',
        'notes',
        'is_active',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the transports for the transporter.
     */
    public function transports(): HasMany
    {
        return $this->hasMany(Transport::class);
    }
    
    /**
     * Get the full name of the transporter including contact person if available.
     */
    public function getFullNameAttribute(): string
    {
        return $this->contact_person 
            ? "{$this->name} ({$this->contact_person})" 
            : $this->name;
    }

    /**
     * Get the batches assigned to this transporter.
     */
    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }
    
    /**
     * Get the gate passes assigned to this transporter.
     */
    public function gatePasses(): HasMany
    {
        return $this->hasMany(GatePass::class);
    }
} 