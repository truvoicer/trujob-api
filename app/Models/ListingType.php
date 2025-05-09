<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingType extends Model
{
    
    public function listings() {
        return $this->belongsToMany(Listing::class, 'listing_listing_types');
    }
}
