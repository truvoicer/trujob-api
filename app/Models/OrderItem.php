<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'order_itemable_id',
        'order_itemable_type',
        'quantity',
    ];
    
    public function orderItemable(): MorphTo
    {
        return $this->morphTo();
    }
}
