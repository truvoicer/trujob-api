<?php
// app/Models/Region.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'country_id',
        'admin_name',
        'name',
        'code',
        'toponym_name',
        'category',
        'description',
        'lng',
        'lat',
        'population',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'lng' => 'float',
        'lat' => 'float',
        'population' => 'integer',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
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

    public function discountables()
    {
        return $this->morphMany(Discountable::class, 'discountable');
    }
}
