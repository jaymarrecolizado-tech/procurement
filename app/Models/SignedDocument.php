<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignedDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_id',
        'signer_id',
        'signer_role',
        'signed_at',
        'document_url',
        'notes',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    /**
     * Get the purchase order for this signed document
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    /**
     * Get the user who signed this document
     */
    public function signer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signer_id');
    }
}

