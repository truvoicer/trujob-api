<?php

namespace App\Models;

use App\Enums\Price\PriceType;
use App\Enums\Product\ProductType;
use Database\Factories\product\ProductFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'title',
        'type',
        'description',
        'active',
        'allow_offers',
        'quantity',
        'has_weight',
        'has_height',
        'has_length',
        'has_width',
        'weight_unit',
        'height_unit',
        'length_unit',
        'width_unit',
        'weight',
        'height',
        'length',
        'width',
    ];

    protected $casts = [
        'active' => 'boolean',
        'allow_offers' => 'boolean',
        'quantity' => 'integer',
        'type' => ProductType::class,
        'has_weight' => 'boolean',
        'has_height' => 'boolean',
        'has_length' => 'boolean',
        'has_width' => 'boolean',
        'weight_unit' => 'string',
        'height_unit' => 'string',
        'length_unit' => 'string',
        'width_unit' => 'string',
        'weight' => 'float',
        'height' => 'float',
        'length' => 'float',
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

    public function shippingMethods() {
        return $this->morphToMany(ShippingMethod::class, 'productable', 'productable_shipping_methods');
    }

    public function getDefaultPrice(?PriceType $priceType): ?Price
    {
        if ($priceType) {
            return $this->prices()
                ->where('is_default', true)
                ->whereRelation('priceType', 'name', $priceType->value)
                ->first();
        }
        return $this->prices
            ->where('is_default', true)
            ->first();
    }
}
