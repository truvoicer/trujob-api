<?php

namespace App\Models;

use Database\Factories\product\BrandFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'label'
    ];

    protected static function newFactory() {
        return BrandFactory::new();
    }

    public function products() {
        return $this->belongsToMany(Product::class, 'product_brands');
    }
}
