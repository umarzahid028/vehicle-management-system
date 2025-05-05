<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use App\Notifications\NewBatchAssigned;

class Batch extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        // Add global scope to filter batches for transporters
        static::addGlobalScope('transporter_access', function (Builder $builder) {
            if (auth()->check() && auth()->user()->hasRole('Transporter')) {
                $builder->where(function($query) {
                    $query->where('transporter_id', auth()->user()->transporter_id)
                          ->orWhereHas('transports', function($q) {
                              $q->where('transporter_id', auth()->user()->transporter_id);
                          });
                });
            }
        });
    }

    protected static function booted()
    {
        static::updated(function ($batch) {
            // Check if transporter_id was changed and is not null
            if ($batch->isDirty('transporter_id') && $batch->transporter_id) {
                if ($batch->transporter && $batch->transporter->user) {
                    $batch->transporter->user->notify(new NewBatchAssigned($batch));
                }
            }
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'batch_number',
        'name',
        'status',
        'transporter_id',
        'scheduled_pickup_date',
        'scheduled_delivery_date',
        'pickup_date',
        'delivery_date',
        'origin',
        'destination',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'scheduled_pickup_date' => 'date',
        'scheduled_delivery_date' => 'date',
        'pickup_date' => 'datetime',
        'delivery_date' => 'datetime',
    ];

    /**
     * Get the transporter assigned to this batch.
     */
    public function transporter(): BelongsTo
    {
        return $this->belongsTo(Transporter::class);
    }

    /**
     * Get the transports in this batch.
     */
    public function transports(): HasMany
    {
        return $this->hasMany(Transport::class);
    }

    /**
     * Get the vehicles in this batch through transports.
     */
    public function vehicles()
    {
        return $this->hasManyThrough(Vehicle::class, Transport::class, 'batch_id', 'id', 'id', 'vehicle_id');
    }

    /**
     * Get the gate passes for this batch.
     */
    public function gatePasses(): HasMany
    {
        return $this->hasMany(GatePass::class);
    }
    
    /**
     * Get the formatted batch number with name
     */
    public function getFullNameAttribute(): string
    {
        return $this->name 
            ? "{$this->batch_number} - {$this->name}" 
            : $this->batch_number;
    }
    
    /**
     * Get the vehicle count
     */
    public function getVehicleCountAttribute(): int
    {
        return $this->transports()->count();
    }
} 