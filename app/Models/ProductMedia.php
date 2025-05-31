<?php

namespace App\Models;

use Database\Factories\product\ProductMediaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'category',
        'alt',
        'url',
        'path',
        'filesystem'
    ];

    protected static function newFactory()
    {
        return ProductMediaFactory::new();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
