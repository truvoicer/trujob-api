<?php

namespace App\Models;

use App\Enums\Transaction\TransactionAmountType;
use Illuminate\Database\Eloquent\Model;

class TransactionAmount extends Model
{
    protected $fillable = [
        'transaction_id',
        'currency_id',
        'type',
        'amount',
    ];

    protected $casts = [
        'type' => TransactionAmountType::class,
        'amount' => 'decimal:2',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

}
