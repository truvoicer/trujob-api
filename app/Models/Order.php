<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Order extends Model
{
    
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
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
}
