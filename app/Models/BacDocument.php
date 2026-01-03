<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BacDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'document_type',
        'content',
        'procurement_mode',
        'status',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    /**
     * Get the purchase request for this BAC document
     */
    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    /**
     * Get all approval routings for this document
     */
    public function approvalRoutings(): HasMany
    {
        return $this->hasMany(ApprovalRouting::class);
    }

    /**
     * Get document type display name
     */
    public function getDocumentTypeNameAttribute()
    {
        return match($this->document_type) {
            'ABSTRACT_OF_QUOTATIONS' => 'Abstract of Quotations',
            'PRICE_MATRIX' => 'Price Matrix',
            'TWG_CERT' => 'TWG Certificate',
            'RECOMMENDATION' => 'Recommendation',
            'RESOLUTION' => 'Resolution',
            default => $this->document_type
        };
    }

    /**
     * Get procurement mode display name
     */
    public function getProcurementModeNameAttribute()
    {
        return match($this->procurement_mode) {
            'SHOPPING' => 'Shopping',
            'SVP' => 'Small Value Procurement',
            'PUBLIC_BIDDING' => 'Public Bidding',
            'NEGOTIATED' => 'Negotiated',
            'DIRECT_CONTRACTING' => 'Direct Contracting',
            default => $this->procurement_mode
        };
    }

    /**
     * Get status display name
     */
    public function getStatusNameAttribute()
    {
        return match($this->status) {
            'DRAFT' => 'Draft',
            'PENDING_APPROVAL' => 'Pending Approval',
            'APPROVED' => 'Approved',
            'REJECTED' => 'Rejected',
            default => $this->status
        };
    }
}

