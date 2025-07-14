<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Currency extends Model
{
    use HasFactory;
    protected $fillable = [
        'country_id',
        'name',
        'name_plural',
        'code',
        'symbol',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(UserProfile::class);
    }

    public function productTransaction()
    {
        return $this->belongsTo(ProductPrice::class);
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
