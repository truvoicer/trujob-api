<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Listing extends Model
{

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
    public function listingMedia()
    {
        return $this->hasMany(ListingMedia::class);
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
