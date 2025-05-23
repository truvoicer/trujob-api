<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'price_id',
        'payment_gateway_id',
        'user_id',
    ];
    
    public function listings() {
        return $this->belongsToMany(Listing::class, 'listing_transactions');
    }
    
    public function listing() {
        return $this->belongsTo(Listing::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function price() {
        return $this->belongsTo(Price::class);
    }
    
    public function currency() {
        return $this->belongsTo(Currency::class);
    }

    public function paymentGateway() {
        return $this->belongsTo(PaymentGateway::class);
    }

    public function orderTransactions() {
        return $this->hasMany(OrderTransaction::class);
    }
    public function orders() {
        return $this->belongsToMany(Order::class, 'order_transactions');
    }
    
}
