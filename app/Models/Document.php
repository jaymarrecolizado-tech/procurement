<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'rfq_id',
        'po_id',
        'document_type',
        'file_name',
        'file_path',
        'file_size',
        'uploaded_by',
    ];

    /**
     * Get the purchase request (if applicable)
     */
    public function purchaseRequest(): BelongsTo
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    /**
     * Get the RFQ (if applicable)
     */
    public function rfq(): BelongsTo
    {
        return $this->belongsTo(RFQ::class);
    }

    /**
     * Get the purchase order (if applicable)
     */
    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class, 'po_id');
    }

    /**
     * Get the user who uploaded this document
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get document type display name
     */
    public function getDocumentTypeNameAttribute()
    {
        return match($this->document_type) {
            'PR' => 'Purchase Request',
            'RFQ' => 'Request for Quotation',
            'QUOTATION' => 'Quotation',
            'AOQ' => 'Abstract of Quotations',
            'BAC_RESOLUTION' => 'BAC Resolution',
            'PO' => 'Purchase Order',
            'CONFORME' => 'Conforme',
            'COA_PACKET' => 'COA Packet',
            default => $this->document_type
        };
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}

