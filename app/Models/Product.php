<?php

namespace App\Models;

use App\Enums\Price\PriceType;
use App\Enums\Product\ProductType;
use App\Enums\Product\ProductUnit;
use App\Enums\Product\ProductWeightUnit;
use App\Traits\Product\ProductableHealthTrait;
use App\Traits\Product\ProductableTrait;
use Database\Factories\product\ProductFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    use HasFactory, ProductableTrait, ProductableHealthTrait;

    protected $fillable = [
        'name',
        'title',
        'type',
        'description',
        'active',
        'allow_offers',
        'sku',
        'quantity',
        'has_weight',
        'has_height',
        'has_depth',
        'has_width',
        'weight_unit',
        'height_unit',
        'depth_unit',
        'width_unit',
        'weight',
        'height',
        'depth',
        'width',
    ];

    protected $casts = [
        'active' => 'boolean',
        'allow_offers' => 'boolean',
        'quantity' => 'integer',
        'type' => ProductType::class,
        'has_weight' => 'boolean',
        'has_height' => 'boolean',
        'has_depth' => 'boolean',
        'has_width' => 'boolean',
        'weight_unit' => ProductWeightUnit::class,
        'height_unit' => ProductUnit::class,
        'depth_unit' => ProductUnit::class,
        'width_unit' => ProductUnit::class,
        'weight' => 'float',
        'height' => 'float',
        'depth' => 'float',
        'width' => 'float',
    ];

    protected static function newFactory()
    {
        return ProductFactory::new();
    }

    protected function active(): Attribute
    {
        return Attribute::make(
            get: fn($value) => (bool)$value,
        );
    }
    protected function allowOffers(): Attribute
    {
        return Attribute::make(
            get: fn($value) => (bool)$value,
        );
    }
    public function features()
    {
        return $this->belongsToMany(Feature::class, 'product_features');
    }

    public function productReview()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function productFollow()
    {
        return $this->hasMany(ProductFollow::class);
    }

    public function follows()
    {
        return $this->belongsToMany(User::class, 'product_follows');
    }

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'product_transactions');
    }

    public function messagingGroups()
    {
        return $this->hasMany(MessagingGroup::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_products');
    }
    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'product_brands');
    }
    public function colors()
    {
        return $this->belongsToMany(Color::class, 'product_colors');
    }

    public function productCategories()
    {
        return $this->belongsToMany(ProductCategory::class, 'product_product_categories');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->belongsToMany(Media::class);
    }
    public function prices()
    {
        return $this->belongsToMany(Price::class, 'product_prices');
    }

    public function orderItems(): MorphMany
    {
        return $this->morphMany(OrderItem::class, 'order_itemable');
    }

    public function shippingRestrictions(): MorphMany
    {
        return $this->morphMany(ShippingRestriction::class, 'restrictionable');
    }

    public function productableShippingMethods(): MorphMany
    {
        return $this->morphMany(ProductableShippingMethod::class, 'productable');
    }

    public function shippingMethods()
    {
        return $this->morphToMany(ShippingMethod::class, 'productable', 'productable_shipping_methods');
    }

    public function getPriceByPriceType(PriceType $priceType): Price|null
    {
        return $this->prices()
            ->whereRelation('priceType', 'name', $priceType->value)
            ->first();
    }

    public function getPriceByUserLocaleAndPriceType(
        User $user,
        PriceType $priceType
    ): Price|null {
        $country = $user->userSetting->country;
        $currency = $user->userSetting->currency;

        if (!$country || !$currency) {
            return null;
        }

        return $this->prices()
            ->whereRelation('priceType', 'name', $priceType->value)
            // ->whereRelation('country', 'id', $country->id)
            // ->whereRelation('currency', 'id', $currency->id)
            ->first();
    }

}
