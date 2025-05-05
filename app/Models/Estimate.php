<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Estimate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vendor_id',
        'reference_number',
        'estimated_cost',
        'description',
        'status',
        'approved_by_user_id',
        'approved_at',
        'rejected_reason',
    ];

    protected $casts = [
        'estimated_cost' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the vendor that owns this estimate.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the user who approved the estimate.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }
}
