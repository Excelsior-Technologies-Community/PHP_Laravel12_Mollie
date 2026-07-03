<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'payment_id',
        'amount',
        'status',
        'customer_email',
        'refunded_amount',
        'refund_status',
        'promo_code',
        'retry_count',
        'invoice_sent',
    ];

    protected $casts = [
        'invoice_sent' => 'boolean',
        'amount' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
    ];

    public function remainingRefundable()
    {
        return $this->amount - $this->refunded_amount;
    }
}