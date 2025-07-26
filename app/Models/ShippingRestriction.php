<?php

namespace App\Models;

use App\Enums\Order\Shipping\ShippingRestrictionAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ShippingRestriction extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_method_id',
        'restrictionable_id',
        'restrictionable_type',
        'action'
    ];

    protected $casts = [
        'action' => ShippingRestrictionAction::class,
    ];

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function restrictionable(): MorphTo
    {
        return $this->morphTo();
    }
}
