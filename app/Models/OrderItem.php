<?php

namespace App\Models;

use App\Traits\Model\Order\CalculateOrderItemTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderItem extends Model
{
    use CalculateOrderItemTrait;

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
