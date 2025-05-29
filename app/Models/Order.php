<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Order extends Model
{
    
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }
    
    public function orderTransactions() {
        return $this->hasMany(OrderTransaction::class);
    }

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
    public function shipments()
    {
        return $this->hasMany(OrderShipment::class);
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_orders')
            ->withTimestamps();
    }
}
