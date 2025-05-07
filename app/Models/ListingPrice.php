<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingPrice extends Model
{

    protected $fillable = [
        'listing_id',
        'price_id',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function price()
    {
        return $this->belongsTo(Price::class);
    }
}
