<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupplierQuotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'canvass_id',
        'supplier_name',
        'supplier_address',
        'supplier_contact',
        'supplier_email',
        'submitted_date',
        'quote_price',
        'delivery_days',
        'remarks',
        'is_compliant',
        'is_selected',
        'document_url',
    ];

    protected $casts = [
        'submitted_date' => 'date',
        'quote_price' => 'decimal:2',
        'is_compliant' => 'boolean',
        'is_selected' => 'boolean',
    ];

    /**
     * Get the canvass for this quotation
     */
    public function canvass(): BelongsTo
    {
        return $this->belongsTo(Canvass::class);
    }

    /**
     * Get all quotation items
     */
    public function quotationItems(): HasMany
    {
        return $this->hasMany(QuotationItem::class, 'quotation_id');
    }
}

