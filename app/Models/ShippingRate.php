<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_method_id',
        'shipping_zone_id',
        'rate_type',
        'min_value',
        'max_value',
        'rate_amount',
        'currency_id',
        'is_free_shipping_possible'
    ];

    protected $casts = [
        'rate_amount' => 'decimal:2',
        'min_value' => 'decimal:2',
        'max_value' => 'decimal:2',
        'is_free_shipping_possible' => 'boolean',
    ];

    public function method()
    {
        return $this->belongsTo(ShippingMethod::class, 'shipping_method_id');
    }

    public function zone()
    {
        return $this->belongsTo(ShippingZone::class, 'shipping_zone_id');
    }

    public function calculateRate(float $orderValue = null, float $weight = null): float
    {
        return match($this->rate_type) {
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
            ($this->max_value !== null && $weight > $this->max_value)) {
            return 0.00; // Doesn't apply, should be handled by rate selection logic
        }
        
        return $this->rate_amount;
    }
}