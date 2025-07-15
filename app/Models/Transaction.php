<?php

namespace App\Models;

use App\Enums\Transaction\TransactionStatus;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{

    protected $fillable = [
        'user_id',
        'order_id',
        'payment_gateway_id',
        'transaction_id',
        'status',
        'amount',
        'currency_code',
        'order_data',
        'transaction_data',
    ];

    protected $casts = [
        'status' => TransactionStatus::class,
        'amount' => 'decimal:2',
        'transaction_data' => 'array',
        'order_data' => 'array',
        'currency_code' => 'string',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function paymentGateway()
    {
        return $this->belongsTo(PaymentGateway::class);
    }
}
