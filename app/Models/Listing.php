<?php

namespace App\Models;

use Database\Factories\listing\ListingFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
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
        return ListingFactory::new();
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
        return $this->belongsToMany(Feature::class, 'listing_features');
    }

    public function listingReview()
    {
        return $this->hasMany(ListingReview::class);
    }

    public function listingFollow()
    {
        return $this->hasMany(ListingFollow::class);
    }

    public function follows()
    {
        return $this->belongsToMany(User::class, 'listing_follows');
    }

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'listing_transactions');
    }

    public function messagingGroups()
    {
        return $this->hasMany(MessagingGroup::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'listing_categories');
    }
    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'listing_brands');
    }
    public function colors()
    {
        return $this->belongsToMany(Color::class, 'listing_colors');
    }

    public function productTypes()
    {
        return $this->belongsToMany(ProductType::class, 'listing_product_types');
    }

    public function types()
    {
        return $this->belongsToMany(ListingType::class, 'listing_listing_types');
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
        return $this->belongsToMany(Price::class, 'listing_prices');
    }
}
