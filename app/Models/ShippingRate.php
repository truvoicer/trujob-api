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
        'type',
        'weight_limit',
        'height_limit',
        'width_limit',
        'length_limit',
        'weight_unit',
        'height_unit',
        'width_unit',
        'length_unit',
        'min_weight',
        'max_weight',
        'min_height',
        'max_height',
        'min_width',
        'max_width',
        'min_length',
        'max_length',
        'amount',
        'currency_id',
        'is_free_shipping_possible'
    ];

    protected $casts = [
        'type' => ShippingRateType::class,
        'amount' => 'decimal:2',
        'min_weight' => 'decimal:2',
        'max_weight' => 'decimal:2',
        'min_height' => 'decimal:2',
        'max_height' => 'decimal:2',
        'min_width' => 'decimal:2',
        'max_width' => 'decimal:2',
        'min_length' => 'decimal:2',
        'max_length' => 'decimal:2',
        'is_free_shipping_possible' => 'boolean',
        'weight_limit' => 'boolean',
        'height_limit' => 'boolean',
        'width_limit' => 'boolean',
        'length_limit' => 'boolean',
        'weight_unit' => ShippingWeightUnit::class,
        'height_unit' => ShippingUnit::class,
        'width_unit' => ShippingUnit::class,
        'length_unit' => ShippingUnit::class,
    ];

    public function shippingMethod()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function shippingZone()
    {
        return $this->belongsTo(ShippingZone::class);
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
