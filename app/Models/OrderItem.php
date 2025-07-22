<?php

namespace App\Models;

use App\Enums\Order\OrderItemable;
use App\Traits\Model\Order\CalculateOrderItemTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderItem extends Model
{
    use CalculateOrderItemTrait, HasFactory;

    protected $fillable = [
        'order_id',
        'order_itemable_id',
        'order_itemable_type',
        'quantity',
    ];

    protected $casts = [
        'order_itemable_type' => OrderItemable::class,
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function orderItemable(): MorphTo
    {
        return $this->morphTo();
    }



}
