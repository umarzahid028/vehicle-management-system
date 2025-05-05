<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodwillClaim extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'vehicle_id',
        'sales_issue_id',
        'created_by_user_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'issue_description',
        'requested_resolution',
        'customer_consent',
        'customer_consent_date',
        'customer_signature',
        'signed_in_person',
        'signature_date',
        'status',
        'approved_by_user_id',
        'approved_at',
        'approval_notes',
        'estimated_cost',
        'actual_cost',
    ];

    protected $casts = [
        'customer_consent' => 'boolean',
        'customer_consent_date' => 'datetime',
        'signed_in_person' => 'boolean',
        'signature_date' => 'datetime',
        'approved_at' => 'datetime',
        'estimated_cost' => 'decimal:2',
        'actual_cost' => 'decimal:2',
    ];

    /**
     * Get the vehicle associated with the claim.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * Get the sales issue associated with the claim.
     */
    public function salesIssue(): BelongsTo
    {
        return $this->belongsTo(SalesIssue::class);
    }

    /**
     * Get the user who created the claim.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /**
     * Get the user who approved the claim.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by_user_id');
    }

    /**
     * Check if the claim is pending approval.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the claim has been approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if the claim has customer consent.
     */
    public function hasCustomerConsent(): bool
    {
        return $this->customer_consent && $this->customer_consent_date !== null;
    }

    /**
     * Check if the claim has a customer signature.
     */
    public function hasSignature(): bool
    {
        return !empty($this->customer_signature) && $this->signature_date !== null;
    }
} 