<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'subject_type',
        'subject_id',
        'description',
        'properties',
        'ip_address',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the user that performed the activity.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject of the activity.
     */
    public function subject()
    {
        return $this->morphTo();
    }

    /**
     * Log a new activity.
     *
     * @param string $description
     * @param Model|null $subject
     * @param array $properties
     * @return static
     */
    public static function log($description, $subject = null, $properties = [])
    {
        $activity = new static;
        $activity->description = $description;
        $activity->user_id = auth()->id();
        $activity->ip_address = request()->ip();
        
        if ($subject) {
            $activity->subject_type = get_class($subject);
            $activity->subject_id = $subject->id;
        }
        
        $activity->properties = $properties;
        $activity->save();
        
        return $activity;
    }
} 