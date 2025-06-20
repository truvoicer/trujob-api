<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label',
        'description',
        'is_active',
        'all',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'all' => 'boolean',
    ];

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_shipping_zones')
            ->withTimestamps();
    }


    public function rates()
    {
        return $this->hasMany(ShippingRate::class);
    }

    public function methods()
    {
        return $this->belongsToMany(ShippingMethod::class, 'shipping_rates');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function countryInZone(string $countryCode): bool
    {
        return $this->countries()->where('iso_code', $countryCode)->exists();
    }

    public function getCountryCodesAttribute()
    {
        return $this->countries->pluck('iso_code')->toArray();
    }

    public function shippingZoneAbles()
    {
        return $this->morphMany(ShippingZoneAble::class, 'shipping_zoneable');
    }
}
