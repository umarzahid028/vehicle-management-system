<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class RepairImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_item_result_id',
        'image_path',
        'image_type',
        'caption',
    ];

    /**
     * Get the item result that owns this image.
     */
    public function itemResult(): BelongsTo
    {
        return $this->belongsTo(InspectionItemResult::class, 'inspection_item_result_id');
    }

    /**
     * Get the full URL for the image.
     */
    public function getImageUrlAttribute(): string
    {
        return Storage::url($this->image_path);
    }

    /**
     * Check if the image exists in storage.
     */
    public function exists(): bool
    {
        return Storage::exists($this->image_path);
    }

    /**
     * Delete the image from storage when the model is deleted.
     */
    protected static function booted()
    {
        static::deleting(function ($repairImage) {
            if (Storage::exists($repairImage->image_path)) {
                Storage::delete($repairImage->image_path);
            }
        });
    }
} 