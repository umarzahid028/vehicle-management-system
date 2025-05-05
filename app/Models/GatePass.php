<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GatePass extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'pass_number',
        'vehicle_id',
        'transporter_id',
        'batch_id',
        'status',
        'issue_date',
        'expiry_date',
        'used_at',
        'authorized_by',
        'file_path',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'used_at' => 'datetime',
    ];

    /**
     * Get the vehicle this gate pass is for.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the transporter assigned to this gate pass.
     */
    public function transporter(): BelongsTo
    {
        return $this->belongsTo(Transporter::class);
    }

    /**
     * Get the batch this gate pass belongs to.
     */
    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }
    
    /**
     * Check if the gate pass is valid for use
     */
    public function isValid(): bool
    {
        return $this->status === 'approved' && 
               ($this->expiry_date === null || $this->expiry_date->isFuture());
    }
    
    /**
     * Check if the gate pass is expired
     */
    public function isExpired(): bool
    {
        return $this->expiry_date !== null && $this->expiry_date->isPast();
    }
} 