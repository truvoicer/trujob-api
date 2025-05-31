<?php

namespace App\Models;

use Database\Factories\product\ColorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'label'
    ];
    protected static function newFactory() {
        return ColorFactory::new();
    }

    public function products() {
        return $this->belongsToMany(Product::class, 'product_colors');
    }
}
