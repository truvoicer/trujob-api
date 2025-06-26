<?php

namespace App\Models;

use Database\Factories\product\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label'
    ];
    protected static function newFactory()
    {
        return CategoryFactory::new();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_products')
            ->withTimestamps();
    }

    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_categories')
            ->withTimestamps();
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
