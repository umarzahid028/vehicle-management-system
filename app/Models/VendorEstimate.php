<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VendorEstimate extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'inspection_item_result_id',
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

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function inspectionItemResult()
    {
        return $this->belongsTo(InspectionItemResult::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }
}
