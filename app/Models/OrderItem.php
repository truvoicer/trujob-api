<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'productable_id',
        'productable_type',
        'quantity',
    ];
    
    public function productable(): MorphTo
    {
        return $this->morphTo();
    }
}
