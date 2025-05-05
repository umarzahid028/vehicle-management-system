<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VehicleInspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'inspection_stage_id',
        'user_id',
        'vendor_id',
        'status',
        'inspection_date',
        'completed_date',
        'notes',
        'total_cost',
        'meta_data',
    ];

    protected $casts = [
        'inspection_date' => 'datetime',
        'completed_date' => 'datetime',
        'total_cost' => 'decimal:2',
        'meta_data' => 'json',
    ];

    /**
     * Get the vehicle that owns this inspection.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the inspection stage that owns this inspection.
     */
    public function inspectionStage(): BelongsTo
    {
        return $this->belongsTo(InspectionStage::class);
    }

    /**
     * Get the user (inspector) that performed this inspection.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vendor associated with this inspection.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the item results for this inspection.
     */
    public function itemResults(): HasMany
    {
        return $this->hasMany(InspectionItemResult::class);
    }

    /**
     * Alias for itemResults() for backward compatibility
     */
    public function inspectionItems(): HasMany
    {
        return $this->itemResults();
    }

    /**
     * Calculate if the inspection is fully completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed' && $this->completed_date !== null;
    }

    /**
     * Calculate if all items in this inspection have passed.
     */
    public function allItemsPassed(): bool
    {
        if ($this->itemResults->isEmpty()) {
            return false;
        }

        return $this->itemResults->every(function ($result) {
            return $result->status === 'pass' || $result->status === 'not_applicable';
        });
    }

    /**
     * Calculate the total repair cost for this inspection.
     */
    public function calculateTotalCost(): float
    {
        return $this->itemResults->sum('cost');
    }

    /**
     * Count items that require repair.
     */
    public function countItemsRequiringRepair(): int
    {
        return $this->itemResults->where('requires_repair', true)->count();
    }

    /**
     * Count items that have had repairs completed.
     */
    public function countCompletedRepairs(): int
    {
        return $this->itemResults->where('requires_repair', true)
            ->where('repair_completed', true)
            ->count();
    }
} 