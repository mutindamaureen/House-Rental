<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'house_id',
        'landlord_id',
        'national_id',
        'rent',
        'utilities',
        'security_deposit',
        'payment_status',
        'lease_start_date',
        'lease_end_date',
        'lease_status',
        'payment_due_date',
        'emergency_contact_name',
        'emergency_contact_phone',
        'created_by',
        'status',
        'moved_out_at',
    ];

    /**
     * Relationships
     */

    // Each tenant belongs to a user (the tenant's account)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Each tenant rents one house
    public function house()
    {
        return $this->belongsTo(House::class);
    }

    // Each tenant has a landlord (who owns the house)
    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    // The admin or user who created the record
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Accessors (optional helpers)
     */

    // Compute total rent dynamically if needed
    public function getTotalRentAttribute()
    {
        return $this->rent + $this->utilities;
    }

    // Check if tenant's lease is active
    public function getIsActiveAttribute()
    {
        return $this->lease_status === 'active' && $this->status === 'active';
    }
}
