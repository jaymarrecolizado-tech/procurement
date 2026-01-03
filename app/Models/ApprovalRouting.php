<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalRouting extends Model
{
    use HasFactory;

    protected $fillable = [
        'bac_document_id',
        'approver_id',
        'approver_role',
        'sequence',
        'status',
        'signed_at',
        'comments',
        'time_spent_hours',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
        'time_spent_hours' => 'decimal:2',
    ];

    /**
     * Get the BAC document for this approval routing
     */
    public function bacDocument(): BelongsTo
    {
        return $this->belongsTo(BacDocument::class);
    }

    /**
     * Get the approver user
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approver_id');
    }

    /**
     * Get status display name
     */
    public function getStatusNameAttribute()
    {
        return match($this->status) {
            'PENDING' => 'Pending',
            'APPROVED' => 'Approved',
            'REJECTED' => 'Rejected',
            default => $this->status
        };
    }

    /**
     * Check if this approval is pending
     */
    public function isPending()
    {
        return $this->status === 'PENDING';
    }

    /**
     * Check if this approval is approved
     */
    public function isApproved()
    {
        return $this->status === 'APPROVED';
    }
}

