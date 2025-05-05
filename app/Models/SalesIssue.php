<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SalesIssue extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'vehicle_id',
        'reported_by_user_id',
        'issue_type',
        'description',
        'priority',
        'status',
        'reviewed_by_user_id',
        'reviewed_at',
        'review_notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    /**
     * Get the vehicle associated with the sales issue.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the user who reported the issue.
     */
    public function reportedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by_user_id');
    }

    /**
     * Get the user who reviewed the issue.
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by_user_id');
    }

    /**
     * Get the goodwill claim associated with this issue.
     */
    public function goodwillClaim(): HasOne
    {
        return $this->hasOne(GoodwillClaim::class);
    }

    /**
     * Check if the issue is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the issue is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if the issue is resolved.
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    /**
     * Check if the issue is closed.
     */
    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    /**
     * Check if the issue is urgent.
     */
    public function isUrgent(): bool
    {
        return $this->priority === 'urgent';
    }
} 