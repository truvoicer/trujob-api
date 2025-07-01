<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'name',
        'description',
        'is_active',
        'processing_time_days',
        'display_order'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_shipping_methods')
            ->withTimestamps();
    }

    public function rates()
    {
        return $this->hasMany(ShippingRate::class);
    }

    public function restrictions()
    {
        return $this->hasMany(ShippingRestriction::class);
    }

    public function shipments()
    {
        return $this->hasMany(OrderShipment::class);
    }

    public function zones()
    {
        return $this->belongsToMany(ShippingZone::class, 'shipping_rates');
    }

    public function discountables()
    {
        return $this->morphMany(Discountable::class, 'discountable');
    }

    public function productableShippingMethods()
    {
        return $this->morphMany(ProductableShippingMethod::class, 'productable');
    }

    public function tiers()
    {
        return $this->hasMany(ShippingMethodTier::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order');
    }

    protected function isMethodAvailableForOrder(ShippingMethod $method, array $orderData): bool
    {
        // Check restrictions
        if ($method->restrictions->isNotEmpty()) {
            foreach ($method->restrictions as $restriction) {
                if ($restriction->type === 'product' &&
                    in_array($restriction->restriction_id, $orderData['product_ids'] ?? []) &&
                    $restriction->action === 'deny') {
                    return false;
                }
            }
        }

        return true;
    }
}
