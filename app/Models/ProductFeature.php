<?php

namespace App\Models;

use Database\Factories\product\ProductFeatureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFeature extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return ProductFeatureFactory::new();
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
