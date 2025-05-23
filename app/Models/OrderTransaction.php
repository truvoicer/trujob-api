<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderTransaction extends Model
{
    protected $fillable = [
        'order_id',
        'transaction_id',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
