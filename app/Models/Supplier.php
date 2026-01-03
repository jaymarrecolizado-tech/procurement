<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_name',
        'supplier_name_original',
        'supplier_address',
        'supplier_contact',
        'supplier_email',
        'business_registration_number',
        'supplier_category',
        'status',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Get the user who created this supplier
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this supplier
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all quotation history for this supplier
     */
    public function quotationHistory(): HasMany
    {
        return $this->hasMany(SupplierQuotationHistory::class);
    }

    /**
     * Normalize supplier name
     */
    public static function normalizeName(string $name): string
    {
        return strtoupper(trim($name));
    }

    /**
     * Get status display name
     */
    public function getStatusNameAttribute()
    {
        return match($this->status) {
            'ACTIVE' => 'Active',
            'INACTIVE' => 'Inactive',
            default => $this->status
        };
    }
}
