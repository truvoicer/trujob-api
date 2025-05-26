<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiscountShippingMethod extends Model
{
    
    protected $fillable = [
        'shipping_zone_id',
        'shipping_method_id',
    ];

    public function shippingZone()
    {
        return $this->belongsTo(shippingZone::class, 'shipping_zone_id');
    }

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }
}
