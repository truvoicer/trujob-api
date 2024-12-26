<?php

namespace App\Models;

use Database\Factories\listing\ListingProductTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingProductType extends Model
{
    use HasFactory;
    protected static function newFactory() {
        return ListingProductTypeFactory::new();
    }
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }
}
