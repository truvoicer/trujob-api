<?php

namespace App\Models;

use App\Enums\Order\Shipping\ShippingRateType;
use App\Enums\Order\Shipping\ShippingUnit;
use App\Enums\Order\Shipping\ShippingWeightUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_method_id',
        'shipping_zone_id',
        'currency_id',
        'type',
        'name',
        'label',
        'description',
        'is_active',
        'has_max_dimension',
        'max_dimension',
        'max_dimension_unit',
        'has_weight',
        'has_height',
        'has_width',
        'has_depth',
        'weight_unit',
        'max_weight',
        'height_unit',
        'max_height',
        'width_unit',
        'max_width',
        'depth_unit',
        'max_depth',
        'amount',
        'dimensional_weight_divisor',
    ];

    protected $casts = [
        'type' => ShippingRateType::class,
        'is_active' => 'boolean',
        'has_weight' => 'boolean',
        'has_height' => 'boolean',
        'has_width' => 'boolean',
        'has_depth' => 'boolean',
        'has_max_dimension' => 'boolean',
        'max_dimension' => 'float',
        'max_weight' => 'float',
        'max_height' => 'float',
        'max_width' => 'float',
        'max_depth' => 'float',
        'amount' => 'float',
        'dimensional_weight_divisor' => 'float',
        'weight_unit' => ShippingWeightUnit::class,
        'height_unit' => ShippingUnit::class,
        'width_unit' => ShippingUnit::class,
        'depth_unit' => ShippingUnit::class,
        'max_dimension_unit' => ShippingUnit::class
    ];

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function shippingZone()
    {
        return $this->belongsTo(ShippingZone::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function calculateRate(float $orderValue = 0, float $weight = 0): float
    {
        return match ($this->rate_type) {
            'free' => 0.00,
            'flat' => $this->rate_amount,
            'price_based' => ($orderValue >= ($this->min_value ?? 0)) ? 0.00 : $this->rate_amount,
            'weight_based' => $this->calculateWeightBasedRate($weight),
            default => $this->rate_amount
        };
    }

    protected function calculateWeightBasedRate(?float $weight): float
    {
        if ($weight === null) return $this->rate_amount;

        if (($this->min_value !== null && $weight < $this->min_value) ||
            ($this->max_value !== null && $weight > $this->max_value)
        ) {
            return 0.00; // Doesn't apply, should be handled by rate selection logic
        }

        return $this->rate_amount;
    }

    public function calculateShippingRateData(
        Country $country,
        ShippingMethod $shippingMethod,
        ?float $orderAmount = null,
        ?float $weight = null
    ) {
        $zone = ShippingZone::whereHas('countries', function ($query) use ($country) {
            $query->where('countries.id', $country->id);
        })->firstOrFail();

        $rate = ShippingRate::where('shipping_method_id', $shippingMethod->id)
            ->where('shipping_zone_id', $zone->id)
            ->firstOrFail();

        $cost = $rate->calculateRate($orderAmount ?? 0, $weight ?? 0);


        return [
            'shipping_cost' => $cost,
            'currency' => $rate->currency_code,
            'estimated_delivery_days' => $rate->method->processing_time_days,
        ];
    }
}
