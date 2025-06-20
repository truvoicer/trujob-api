<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discount extends Model
{
     use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'label',
        'description',
        'currency_id',
        'type',
        'amount',
        'rate',
        'starts_at',
        'ends_at',
        'is_active',
        'usage_limit',
        'usage_count',
        'per_user_limit',
        'min_order_amount',
        'min_items_quantity',
        'apply_to',
        'code',
        'is_code_required',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'is_active' => 'boolean',
        'is_code_required' => 'boolean',
        'amount' => 'decimal:2',
        'rate' => 'decimal:2',
        'min_order_amount' => 'decimal:2',
    ];

    public function prices()
    {
        return $this->belongsToMany(Price::class, 'discount_prices')
            ->withTimestamps();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'discount_categories')
            ->withTimestamps();
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_discounts')
            ->withPivot('amount', 'type')
            ->withTimestamps();
    }

    public function shippingMethods()
    {
        return $this->belongsToMany(ShippingMethod::class, 'discount_shipping_methods')
            ->withTimestamps();
    }

    public function shippingZones()
    {
        return $this->belongsToMany(ShippingZone::class, 'discount_shipping_zones')
            ->withTimestamps();
    }


    public function usages()
    {
        return $this->hasMany(UserDiscountUsage::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>=', now());
    }

    public function scopeCode($query, $code)
    {
        return $query->whereHas('currency', function ($query) use ($code) {
            $query->where('code', $code);
        });
    }

    public function isValid(): bool
    {
        return $this->is_active
            && now()->between($this->starts_at, $this->ends_at)
            && ($this->usage_limit === null || $this->usage_count < $this->usage_limit);
    }

    public function default() {
        return $this->hasOne(DefaultDiscount::class, 'discount_id');
    }

    public function isDefault(): bool
    {
        return $this->default()->exists();
    }
}
