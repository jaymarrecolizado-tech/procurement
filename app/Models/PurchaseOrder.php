<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'purchase_request_id',
        'supplier_id',
        'supplier_name',
        'supplier_address',
        'supplier_contact',
        'contract_amount',
        'delivery_instructions',
        'payment_terms',
        'delivery_deadline',
        'status',
        'coa_stamp_reference',
        'coa_stamp_date',
        'notes',
    ];

    protected $casts = [
        'contract_amount' => 'decimal:2',
        'delivery_deadline' => 'date',
        'coa_stamp_date' => 'date',
    ];

    /**
     * Get the purchase request for this PO
     */
    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    /**
     * Get the supplier user
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'supplier_id');
    }

    /**
     * Get all signed documents for this PO
     */
    public function signedDocuments(): HasMany
    {
        return $this->hasMany(SignedDocument::class, 'po_id');
    }

    /**
     * Get all documents for this PO
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'po_id');
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
            'DISSEMINATED' => 'Disseminated',
            'AWAITING_CONFORME' => 'Awaiting Conforme',
            'COMPLETE' => 'Complete',
            default => $this->status
        };
    }

    /**
     * Generate unique PO number
     */
    public static function generatePoNumber()
    {
        $year = date('Y');
        $lastPo = static::whereYear('created_at', $year)
                       ->orderBy('id', 'desc')
                       ->first();
        
        $sequence = $lastPo ? (int) substr($lastPo->po_number, -4) + 1 : 1;
        return "PO-{$year}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}

