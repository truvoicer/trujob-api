<?php

namespace App\Models;

use Database\Factories\product\ProductCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;
    protected static function newFactory() {
        return ProductCategoryFactory::new();
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
