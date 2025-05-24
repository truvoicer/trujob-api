<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'itemable_id',
        'itemable_type',
        'quantity',
    ];
    
    public function orderItemable(): MorphTo
    {
        return $this->morphTo();
    }
}
