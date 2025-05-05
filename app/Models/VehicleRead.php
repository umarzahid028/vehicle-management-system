<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleRead extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_id',
    ];

    /**
     * Get the user that read the vehicle.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the vehicle that was read.
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }
} 