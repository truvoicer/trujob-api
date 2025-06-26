<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductProductCategory extends Model
{
    //
    protected $fillable = [
        'product_id',
        'product_category_id',
    ];
    protected $table = 'product_product_category';

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
    public function productCategory()
    {
        return $this->belongsTo(ProductCategory::class, 'product_category_id');
    }
}
