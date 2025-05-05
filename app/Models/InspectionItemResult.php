<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InspectionItemResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_inspection_id',
        'inspection_item_id',
        'status',
        'notes',
        'cost',
        'actual_cost',
        'completion_notes',
        'vendor_id',
        'requires_repair',
        'repair_completed',
        'diagnostic_status',
        'is_vendor_visible',
        'assigned_at',
        'completed_at',
        'photo_path',
    ];

    protected $casts = [
        'cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
        'requires_repair' => 'boolean',
        'repair_completed' => 'boolean',
        'is_vendor_visible' => 'boolean',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the vehicle inspection that owns this result.
     */
    public function vehicleInspection(): BelongsTo
    {
        return $this->belongsTo(VehicleInspection::class);
    }

    /**
     * Get the inspection item that this result is for.
     */
    public function inspectionItem(): BelongsTo
    {
        return $this->belongsTo(InspectionItem::class);
    }

    /**
     * Get the vendor assigned to repair this item.
     */
    public function assignedVendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class, 'vendor_id');
    }

    /**
     * Get the repair images for this result.
     */
    public function repairImages(): HasMany
    {
        return $this->hasMany(RepairImage::class);
    }

    /**
     * Get the vendor estimates for this result.
     */
    public function vendorEstimates(): HasMany
    {
        return $this->hasMany(VendorEstimate::class);
    }

    /**
     * Get before repair images.
     */
    public function beforeImages()
    {
        return $this->repairImages()->where('image_type', 'before');
    }

    /**
     * Get after repair images.
     */
    public function afterImages()
    {
        return $this->repairImages()->where('image_type', 'after');
    }

    /**
     * Get documentation images.
     */
    public function documentationImages()
    {
        return $this->repairImages()->where('image_type', 'documentation');
    }

    /**
     * Determine if this item has failed.
     */
    public function hasFailed(): bool
    {
        return $this->status === 'fail';
    }

    /**
     * Determine if this item has passed.
     */
    public function hasPassed(): bool
    {
        return $this->status === 'pass';
    }

    /**
     * Determine if this item needs repair.
     */
    public function needsRepair(): bool
    {
        return $this->status === 'warning';
    }

    /**
     * Get the formatted status label.
     */
    public function getStatusLabel(): string
    {
        return match($this->status) {
            // Sales Manager statuses
            'pass' => 'Pass',
            'warning' => 'Repair',
            'fail' => 'Replace',
            'not_applicable' => 'Not Applicable',
            
            // Vendor statuses
            'in_progress' => 'In Progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            
            default => ucfirst($this->status),
        };
    }

    /**
     * Get the status badge CSS classes.
     */
    public function getStatusBadgeClasses(): string
    {
        return match($this->status) {
            // Sales Manager statuses
            'pass' => 'bg-green-100 text-green-800',
            'warning' => 'bg-yellow-100 text-yellow-800',
            'fail' => 'bg-red-100 text-red-800',
            
            // Vendor statuses
            'in_progress' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-gray-100 text-gray-800',
            
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Check if this item has a sales manager status.
     */
    public function hasSalesManagerStatus(): bool
    {
        return in_array($this->status, ['pass', 'warning', 'fail', 'not_applicable']);
    }

    /**
     * Check if this item has a vendor status.
     */
    public function hasVendorStatus(): bool
    {
        return in_array($this->status, ['in_progress', 'completed', 'cancelled']);
    }

    /**
     * Check if this item has a pending estimate.
     */
    public function isPendingEstimate(): bool
    {
        return $this->vendorEstimates()->where('status', 'pending')->exists();
    }

    /**
     * Get the latest vendor estimate.
     */
    public function latestEstimate()
    {
        return $this->vendorEstimates()->latest()->first();
    }
} 