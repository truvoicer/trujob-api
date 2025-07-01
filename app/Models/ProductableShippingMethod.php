<?php

namespace App\Models;

use App\Enums\Product\Shipping\Method\ProductableShippingMethodType;
use Illuminate\Database\Eloquent\Model;

class ProductableShippingMethod extends Model
{
    protected $fillable = [
        'shipping_method_id',
        'productable_id',
        'productable_type',
    ];
    protected $casts = [
        'productable_type' => ProductableShippingMethodType::class,
    ];

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function productable()
    {
        return $this->morphTo();
    }
}
