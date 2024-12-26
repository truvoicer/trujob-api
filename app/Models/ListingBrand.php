<?php

namespace App\Models;

use Database\Factories\listing\ListingBrandFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingBrand extends Model
{
    use HasFactory;

    protected static function newFactory() {
        return ListingBrandFactory::new();
    }
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
