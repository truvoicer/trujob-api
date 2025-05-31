<?php

namespace App\Models;

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
        'description',
        'active',
        'allow_offers',
        'quantity',
    ];

    protected $casts = [
        'active' => 'boolean',
        'allow_offers' => 'boolean',
        'quantity' => 'integer',
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
        return $this->belongsToMany(Category::class, 'product_categories');
    }
    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'product_brands');
    }
    public function colors()
    {
        return $this->belongsToMany(Color::class, 'product_colors');
    }

    public function productTypes()
    {
        return $this->belongsToMany(ProductType::class, 'product_product_types');
    }

    public function types()
    {
        return $this->belongsToMany(ProductType::class, 'product_product_types');
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
        return $this->morphMany(OrderItem::class, 'productable');
    }
    
    public function taxRates(): MorphMany
    {
        return $this->morphMany(PriceTaxRate::class, 'product_tax_rateable');
    }
    
    public function shippingRestrictions(): MorphMany
    {
        return $this->morphMany(ShippingRestriction::class, 'restrictionable');
    }
}
