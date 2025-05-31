<?php

namespace App\Models;

use Database\Factories\product\ProductFollowFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductFollow extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
    ];

    protected static function newFactory()
    {
        return ProductFollowFactory::new();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
