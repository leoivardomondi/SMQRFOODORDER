<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $table = "transactions";
    protected $fillable = ['order_id', 'transaction_no', 'provider_receipt', 'provider_name', 'payer_phone', 'payer_phone_last4', 'amount', 'payment_method', 'payment_channel', 'type', 'sign'];
    protected $casts = [
        'id'             => 'integer',
        'order_id'       => 'integer',
        'transaction_no' => 'string',
        'provider_receipt' => 'string',
        'provider_name' => 'string',
        'payer_phone' => 'string',
        'payer_phone_last4' => 'string',
        'amount'         => 'decimal:6',
        'payment_method' => 'string',
        'payment_channel' => 'string',
        'type'           => 'string',
        'sign'           => 'string',
    ];

    public function order() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }
}
