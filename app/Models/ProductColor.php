<?php

namespace App\Models;

use Database\Factories\product\ProductColorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductColor extends Model
{
    use HasFactory;
    protected static function newFactory() {
        return ProductColorFactory::new();
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
