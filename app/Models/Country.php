<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Country extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'iso2',
        'iso3',
        'phone_code',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    public function user()
    {
        return $this->belongsTo(UserProfile::class);
    }

    public function currency()
    {
        return $this->hasOne(Currency::class);
    }

    public function productTransaction()
    {
        return $this->belongsTo(ProductPrice::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }


    public function regions()
    {
        return $this->hasMany(Region::class);
    }

    public function shippingRestrictions(): MorphMany
    {
        return $this->morphMany(ShippingRestriction::class, 'restrictionable');
    }

    public function taxRateAbles()
    {
        return $this->morphMany(TaxRateAble::class, 'tax_rateable');
    }

    public function shippingZoneAbles()
    {
        return $this->morphMany(ShippingZoneAble::class, 'shipping_zoneable');
    }
}
