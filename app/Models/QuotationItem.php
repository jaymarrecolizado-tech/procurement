<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'pr_item_id',
        'item_name',
        'item_description',
        'quantity',
        'unit_of_measure',
        'unit_price',
        'total_price',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Get the supplier quotation that owns this item
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(SupplierQuotation::class, 'quotation_id');
    }

    /**
     * Get the PR item this quotation item is based on
     */
    public function prItem(): BelongsTo
    {
        return $this->belongsTo(PrItem::class, 'pr_item_id');
    }
}

