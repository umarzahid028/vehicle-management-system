<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleImage extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'image_url',
        'title',
        'description',
        'sort_order',
        'is_featured'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];
    
    /**
     * Get the vehicle that owns this image.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the full image URL
     */
    public function getImageUrlAttribute($value)
    {
        // If it's already a full URL, return it directly
        if (strpos($value, 'http') === 0) {
            return $value;
        }
        
        // If it exists in storage, return the URL
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($value)) {
            return \Illuminate\Support\Facades\Storage::url($value);
        }
        
        // Return default placeholder
        return asset('images/no-image.png');
    }
}
