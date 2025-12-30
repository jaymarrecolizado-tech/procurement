<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'item_code',
        'item_name',
        'item_description',
        'quantity',
        'unit_of_measure',
        'estimated_price',
        'total_estimated',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'estimated_price' => 'decimal:2',
        'total_estimated' => 'decimal:2',
    ];

    /**
     * Get the purchase request that owns this item
     */
    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }
}