<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_shipping_zones')
            ->withPivot('amount', 'type')
            ->withTimestamps();
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'shipping_zone_countries');
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
}