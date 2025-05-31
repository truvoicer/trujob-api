<?php

namespace App\Models;

use Database\Factories\product\ProductReviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'review',
    ];
    protected static function newFactory()
    {
        return ProductReviewFactory::new();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
