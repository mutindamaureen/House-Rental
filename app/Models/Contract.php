<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_id',
        'tenant_id',
        'house_id',
        'contract_pdf',
        'tenant_signature',
        'signed_at',
        'status',
    ];

    protected $casts = [
        'signed_at' => 'datetime',
    ];

    /**
     * Get the landlord associated with this contract
     */
    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * Get the tenant associated with this contract
     */
    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    /**
     * Get the house associated with this contract
     */
    public function house()
    {
        return $this->belongsTo(House::class);
    }

    /**
     * Scope to get pending contracts
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get signed contracts
     */
    public function scopeSigned($query)
    {
        return $query->where('status', 'signed');
    }

    /**
     * Check if contract is signed
     */
    public function isSigned()
    {
        return $this->status === 'signed';
    }

    /**
     * Check if contract is pending
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * Get the contract PDF URL
     */
    public function getPdfUrlAttribute()
    {
        return $this->contract_pdf ? asset('contracts/' . $this->contract_pdf) : null;
    }

    /**
     * Get the signature URL
     */
    public function getSignatureUrlAttribute()
    {
        return $this->tenant_signature ? asset('signatures/' . $this->tenant_signature) : null;
    }
}
