<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ShippingZoneAble extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_zone_id',
        'shipping_zoneable_id',
        'shipping_zoneable_type',
    ];

    public function shippingZone()
    {
        return $this->belongsTo(ShippingZone::class);
    }

    public function shippingZoneAble(): MorphTo
    {
        return $this->morphTo('shipping_zoneable', 'shipping_zoneable_type', 'shipping_zoneable_id');
    }


}
