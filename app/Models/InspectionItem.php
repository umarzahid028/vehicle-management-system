<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_stage_id',
        'name',
        'slug',
        'description',
        'is_active',
        'order',
        'vendor_required',
        'cost_tracking',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'vendor_required' => 'boolean',
        'cost_tracking' => 'boolean',
    ];

    /**
     * Get the inspection stage that owns this item.
     */
    public function inspectionStage(): BelongsTo
    {
        return $this->belongsTo(InspectionStage::class);
    }

    /**
     * Get the inspection results for this item.
     */
    public function itemResults(): HasMany
    {
        return $this->hasMany(InspectionItemResult::class);
    }
} 