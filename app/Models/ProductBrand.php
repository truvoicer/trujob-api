<?php

namespace App\Models;

use Database\Factories\product\ProductBrandFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductBrand extends Model
{
    use HasFactory;

    protected static function newFactory() {
        return ProductBrandFactory::new();
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
