<?php

namespace App\Models;

use Database\Factories\product\ProductTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'label'
    ];
    protected static function newFactory() {
        return ProductTypeFactory::new();
    }


    public function products() {
        return $this->belongsToMany(Product::class, 'product_product_types');
    }
}
