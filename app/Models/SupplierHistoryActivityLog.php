<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierHistoryActivityLog extends Model
{
    use HasFactory;

    protected $table = 'supplier_history_activity_logs';

    protected $fillable = [
        'quotation_history_id',
        'action',
        'field_name',
        'old_value',
        'new_value',
        'changed_by',
    ];

    /**
     * Get the quotation history this log belongs to
     */
    public function quotationHistory(): BelongsTo
    {
        return $this->belongsTo(SupplierQuotationHistory::class, 'quotation_history_id');
    }

    /**
     * Get the user who made the change
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Get action display name
     */
    public function getActionNameAttribute()
    {
        return match($this->action) {
            'CREATED' => 'Created',
            'UPDATED' => 'Updated',
            'DELETED' => 'Deleted',
            default => $this->action
        };
    }
}
