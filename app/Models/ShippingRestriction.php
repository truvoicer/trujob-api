<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ShippingRestriction extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_method_id',
        'type',
        'restriction_id',
        'action'
    ];

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function restrictable(): MorphTo
    {
        return $this->morphTo();
    }
}