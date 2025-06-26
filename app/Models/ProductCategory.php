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

    protected $fillable = [
        'name',
        'label',
        'description',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_product_categories');
    }
}
