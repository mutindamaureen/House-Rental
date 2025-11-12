<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoices extends Model
{
    protected $fillable = [
        'reference',
        'tenant_id',
        'house_id',
        'amount',
        'currency',
        'description',
        'status',
        'due_date',
        'issued_date',
        'paid_amount',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'metadata' => 'array',
        'due_date' => 'date',
        'issued_date' => 'date',
    ];

    // Relations
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function house(): BelongsTo
    {
        return $this->belongsTo(House::class, 'house_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payments::class, 'invoice_id');
    }

    // Helpers
    public function isPaid(): bool
    {
        return $this->status === 'paid' || $this->paid_amount >= $this->amount;
    }

    public static function generateReference(string $prefix = 'INV'): string
    {
        return sprintf('%s-%s-%s', $prefix, date('Ymd'), strtoupper(uniqid()));
    }
}



