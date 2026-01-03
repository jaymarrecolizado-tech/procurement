<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RFQ extends Model
{
    use HasFactory;

    protected $table = 'rfqs';

    protected $fillable = [
        'rfq_number',
        'purchase_request_id',
        'procurement_officer_id',
        'delivery_schedule',
        'payment_terms',
        'canvassing_deadline',
        'status',
        'notes',
    ];

    protected $casts = [
        'canvassing_deadline' => 'date',
    ];

    /**
     * Get the purchase request for this RFQ
     */
    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    /**
     * Get the procurement officer who created this RFQ
     */
    public function procurementOfficer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'procurement_officer_id');
    }

    /**
     * Get all canvasses for this RFQ
     */
    public function canvasses(): HasMany
    {
        return $this->hasMany(Canvass::class);
    }

    /**
     * Get all approval routings for this RFQ
     */
    public function approvalRoutings(): HasMany
    {
        return $this->hasMany(ApprovalRouting::class);
    }

    /**
     * Get all documents for this RFQ
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get status display name
     */
    public function getStatusNameAttribute()
    {
        return match($this->status) {
            'PENDING' => 'Pending',
            'ACTIVE' => 'Active',
            'COMPLETED' => 'Completed',
            default => $this->status
        };
    }

    /**
     * Generate unique RFQ number
     */
    public static function generateRfqNumber()
    {
        $year = date('Y');
        $lastRfq = static::whereYear('created_at', $year)
                       ->orderBy('id', 'desc')
                       ->first();
        
        $sequence = $lastRfq ? (int) substr($lastRfq->rfq_number, -4) + 1 : 1;
        return "RFQ-{$year}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}

