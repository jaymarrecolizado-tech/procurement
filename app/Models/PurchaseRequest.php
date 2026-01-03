<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'pr_number',
        'project_title',
        'project_description',
        'end_user_id',
        'end_user_department',
        'fund_source',
        'estimated_budget',
        'urgency_level',
        'urgency_timeline',
        'approval_date',
        'status',
        'has_signatures',
        'has_specs',
        'has_quantity',
        'has_market_survey',
        'deficiency_notes',
        'purpose',
        'requested_by_name',
        'requested_by_designation',
        'approved_by_name',
        'approved_by_designation',
        'budget_officer_name',
        'budget_officer_designation',
        'office_address',
        'office_name',
        'responsibility_center',
    ];

    protected $casts = [
        'estimated_budget' => 'decimal:2',
        'approval_date' => 'date',
        'has_signatures' => 'boolean',
        'has_specs' => 'boolean',
        'has_quantity' => 'boolean',
        'has_market_survey' => 'boolean',
    ];

    /**
     * Get the end user who created this PR
     */
    public function endUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'end_user_id');
    }

    /**
     * Get all PR items
     */
    public function prItems(): HasMany
    {
        return $this->hasMany(PrItem::class);
    }

    /**
     * Get the associated RFQ
     */
    public function rfq(): HasOne
    {
        return $this->hasOne(RFQ::class);
    }

    /**
     * Get all BAC documents for this PR
     */
    public function bacDocuments(): HasMany
    {
        return $this->hasMany(BacDocument::class);
    }

    /**
     * Get the associated purchase order
     */
    public function purchaseOrder(): HasOne
    {
        return $this->hasOne(PurchaseOrder::class);
    }

    /**
     * Get all documents for this PR
     */
    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    /**
     * Get PR status display name
     */
    public function getStatusNameAttribute()
    {
        return match($this->status) {
            'PR_UNDER_REVIEW' => 'PR Under Review',
            'RFQ_READY' => 'RFQ Ready',
            'RFQ_DISSEMINATED' => 'RFQ Disseminated',
            'CANVASS_COMPLETE' => 'Canvass Complete',
            'BAC_DOCS_READY' => 'BAC Docs Ready',
            'BAC_APPROVED' => 'BAC Approved',
            'PO_APPROVED' => 'PO Approved',
            'AWAITING_CONFORME' => 'Awaiting Conforme',
            'PO_COMPLETE' => 'PO Complete',
            'COA_STAMPED' => 'COA Stamped',
            default => $this->status
        };
    }

    /**
     * Get urgency level display name
     */
    public function getUrgencyLevelNameAttribute()
    {
        return match($this->urgency_level) {
            'LOW' => 'Low',
            'MEDIUM' => 'Medium',
            'HIGH' => 'High',
            'URGENT' => 'Urgent',
            default => $this->urgency_level
        };
    }

    /**
     * Check if PR is complete
     */
    public function isComplete()
    {
        return $this->has_signatures && 
               $this->has_specs && 
               $this->has_quantity && 
               $this->has_market_survey;
    }

    /**
     * Generate unique PR number
     */
    public static function generatePrNumber()
    {
        $year = date('Y');
        $lastPr = static::whereYear('created_at', $year)
                       ->orderBy('id', 'desc')
                       ->first();
        
        $sequence = $lastPr ? (int) substr($lastPr->pr_number, -4) + 1 : 1;
        return "PR-{$year}-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}