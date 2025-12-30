<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'department',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Check if user has a specific role
     */
    public function hasRole($role)
    {
        return $this->role === $role;
    }

    /**
     * Check if user has any of the specified roles
     */
    public function hasAnyRole($roles)
    {
        return in_array($this->role, $roles);
    }

    /**
     * Get the role display name
     */
    public function getRoleNameAttribute()
    {
        return match($this->role) {
            'END_USER' => 'End User',
            'PROCUREMENT_OFFICER' => 'Procurement Officer',
            'BAC_SECRETARIAT' => 'BAC Secretariat',
            'BAC_CHAIR' => 'BAC Chair',
            'BAC_MEMBER' => 'BAC Member',
            'CANVASSER' => 'Canvasser',
            'SUPPLIER' => 'Supplier',
            'ADMIN' => 'Administrator',
            default => $this->role
        };
    }

    /**
     * Relationships
     */
    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class, 'end_user_id');
    }

    public function rfqs()
    {
        return $this->hasMany(RFQ::class, 'procurement_officer_id');
    }

    public function canvasses()
    {
        return $this->hasMany(Canvass::class, 'canvasser_id');
    }

    public function supplierQuotations()
    {
        return $this->hasMany(SupplierQuotation::class);
    }

    public function bacDocuments()
    {
        return $this->hasMany(BacDocument::class);
    }

    public function approvalRoutings()
    {
        return $this->hasMany(ApprovalRouting::class, 'approver_id');
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'supplier_id');
    }

    public function signedDocuments()
    {
        return $this->hasMany(SignedDocument::class, 'signer_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class, 'uploaded_by');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class, 'user_id');
    }
}
