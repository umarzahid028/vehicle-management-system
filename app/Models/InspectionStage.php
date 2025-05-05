<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    /**
     * Get the inspection items for this stage.
     */
    public function inspectionItems(): HasMany
    {
        return $this->hasMany(InspectionItem::class)->orderBy('id');
    }

    /**
     * Get the vehicle inspections for this stage.
     */
    public function vehicleInspections(): HasMany
    {
        return $this->hasMany(VehicleInspection::class);
    }
} 