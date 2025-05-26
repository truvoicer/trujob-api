<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingRestriction extends Model
{
    use HasFactory;

    protected $fillable = [
        'shipping_method_id',
        'type',
        'restriction_id',
        'action'
    ];

    public function method()
    {
        return $this->belongsTo(ShippingMethod::class);
    }

    public function product()
    {
        return $this->type === 'product' 
            ? $this->belongsTo(Product::class, 'restriction_id')
            : null;
    }

    public function category()
    {
        return $this->type === 'category' 
            ? $this->belongsTo(Category::class, 'restriction_id')
            : null;
    }
}