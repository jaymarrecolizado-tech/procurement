<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class SupplierQuotationHistory extends Model
{
    use HasFactory;

    protected $table = 'supplier_quotation_history';

    protected $fillable = [
        'supplier_id',
        'rfq_id',
        'canvass_id',
        'quotation_id',
        'item_name',
        'item_code',
        'item_description',
        'quantity',
        'unit_of_measure',
        'unit_price',
        'total_price',
        'quotation_date',
        'rfq_number',
        'delivery_days',
        'payment_terms',
        'validity_period',
        'remarks',
        'is_selected',
        'entered_by',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'quotation_date' => 'date',
        'validity_period' => 'date',
        'is_selected' => 'boolean',
    ];

    /**
     * Get the supplier
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the RFQ (if linked)
     */
    public function rfq(): BelongsTo
    {
        return $this->belongsTo(RFQ::class);
    }

    /**
     * Get the canvass (if linked)
     */
    public function canvass(): BelongsTo
    {
        return $this->belongsTo(Canvass::class);
    }

    /**
     * Get the original quotation (if linked)
     */
    public function quotation(): BelongsTo
    {
        return $this->belongsTo(SupplierQuotation::class, 'quotation_id');
    }

    /**
     * Get the user who entered this quotation
     */
    public function enteredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    /**
     * Get all images for this quotation
     */
    public function images(): HasMany
    {
        return $this->hasMany(QuotationImage::class, 'quotation_history_id');
    }

    /**
     * Get all activity logs for this quotation
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(SupplierHistoryActivityLog::class, 'quotation_history_id');
    }

    /**
     * Generate item code from item name
     */
    public static function generateItemCode(string $itemName): string
    {
        // Remove special characters, convert to uppercase, replace spaces with hyphens
        $code = preg_replace('/[^A-Za-z0-9\s]/', '', $itemName);
        $code = strtoupper(trim($code));
        $code = preg_replace('/\s+/', '-', $code);
        $code = preg_replace('/-+/', '-', $code); // Remove multiple hyphens
        return $code;
    }

    /**
     * Get quotation age in human readable format
     */
    public function getQuotationAgeAttribute()
    {
        $days = Carbon::parse($this->quotation_date)->diffInDays(now());
        
        if ($days < 7) {
            return $days . ' day' . ($days != 1 ? 's' : '');
        } elseif ($days < 30) {
            $weeks = floor($days / 7);
            return $weeks . ' week' . ($weeks != 1 ? 's' : '');
        } elseif ($days < 365) {
            $months = floor($days / 30);
            return $months . ' month' . ($months != 1 ? 's' : '');
        } else {
            $years = floor($days / 365);
            return $years . ' year' . ($years != 1 ? 's' : '');
        }
    }

    /**
     * Get quotation age in days
     */
    public function getQuotationAgeDaysAttribute()
    {
        return Carbon::parse($this->quotation_date)->diffInDays(now());
    }
}
