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
    ];

    protected static function newFactory()
    {
        return ListingFactory::new();
    }

    protected function active(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (bool)$value,
        );
    }
    protected function allowOffers(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => (bool)$value,
        );
    }
    public function listingFeature()
    {
        return $this->hasMany(ListingFeature::class);
    }

    public function listingReview()
    {
        return $this->hasMany(ListingReview::class);
    }
    
    public function listingFollow()
    {
        return $this->hasMany(ListingFollow::class);
    }
    public function listingTransaction()
    {
        return $this->hasMany(ListingPrice::class);
    }

    public function listingMessagingGroup()
    {
        return $this->hasMany(MessagingGroup::class);
    }

    public function listingCategory()
    {
        return $this->hasMany(ListingCategory::class);
    }
    public function listingBrand()
    {
        return $this->hasMany(ListingBrand::class);
    }
    public function listingColor()
    {
        return $this->hasMany(ListingColor::class);
    }
    public function listingProductType()
    {
        return $this->hasMany(ListingProductType::class);
    }
    public function listingType()
    {
        return $this->belongsTo(ListingType::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function media()
    {
        return $this->belongsToMany(Media::class);
    }
}
