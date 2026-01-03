<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_history_id',
        'image_path',
        'original_filename',
        'file_size',
        'mime_type',
        'uploaded_by',
    ];

    /**
     * Get the quotation history this image belongs to
     */
    public function quotationHistory(): BelongsTo
    {
        return $this->belongsTo(SupplierQuotationHistory::class, 'quotation_history_id');
    }

    /**
     * Get the user who uploaded this image
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get file size in human readable format
     */
    public function getFileSizeHumanAttribute()
    {
        $bytes = $this->file_size ?? 0;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
