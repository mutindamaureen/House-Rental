<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'invoice_id','tenant_id','amount','currency','payment_method','status',
        'gateway','gateway_transaction_id','merchant_reference','request_payload',
        'response_payload','fees_amount','net_amount','settlement_date','attempts',
        'paid_at','refunded_at','idempotency_key','notes','metadata'
    ];

    protected $casts = [
        'request_payload' => 'array',
        'response_payload' => 'array',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoices::class, 'invoice_id');
    }
}


