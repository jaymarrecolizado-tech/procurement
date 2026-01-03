<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Canvass extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfq_id',
        'canvasser_id',
        'task_description',
        'deadline',
        'status',
        'notes',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    /**
     * Get the RFQ for this canvass
     */
    public function rfq(): BelongsTo
    {
        return $this->belongsTo(RFQ::class);
    }

    /**
     * Get the canvasser assigned to this task
     */
    public function canvasser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'canvasser_id');
    }

    /**
     * Get all supplier quotations for this canvass
     */
    public function supplierQuotations(): HasMany
    {
        return $this->hasMany(SupplierQuotation::class);
    }

    /**
     * Get status display name
     */
    public function getStatusNameAttribute()
    {
        return match($this->status) {
            'PENDING' => 'Pending',
            'IN_PROGRESS' => 'In Progress',
            'COMPLETED' => 'Completed',
            'OVERDUE' => 'Overdue',
            default => $this->status
        };
    }

    /**
     * Check if canvass is overdue
     */
    public function isOverdue()
    {
        return $this->deadline < now() && $this->status !== 'COMPLETED';
    }
}

