<?php

namespace App\Models;

use App\Enums\Order\Shipping\ShippingUnit;
use App\Enums\Order\Shipping\ShippingWeightUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethodTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'description',
        'shipping_method_id',
        'is_active',
        'has_max_dimension',
        'max_dimension',
        'max_dimension_unit',
        'has_weight',
        'has_height',
        'has_width',
        'has_length',
        'weight_unit',
        'max_weight',
        'height_unit',
        'max_height',
        'width_unit',
        'max_width',
        'length_unit',
        'max_length',
        'base_amount',
        'dimensional_weight_divisor',
        'currency_id'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'has_weight' => 'boolean',
        'has_height' => 'boolean',
        'has_width' => 'boolean',
        'has_length' => 'boolean',
        'has_max_dimension' => 'boolean',
        'max_dimension' => 'float',
        'max_weight' => 'float',
        'max_height' => 'float',
        'max_width' => 'float',
        'max_length' => 'float',
        'base_amount' => 'float',
        'dimensional_weight_divisor' => 'float',
        'weight_unit' => ShippingWeightUnit::class,
        'height_unit' => ShippingUnit::class,
        'width_unit' => ShippingUnit::class,
        'length_unit' => ShippingUnit::class,
        'max_dimension_unit' => ShippingUnit::class
    ];

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

}
