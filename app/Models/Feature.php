<?php

namespace App\Models;

use Database\Factories\product\FeatureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{

    use HasFactory;
    protected $fillable = [
        'name',
        'label'
    ];

    protected static function newFactory() {
        return FeatureFactory::new();
    }

    public function products() {
        return $this->belongsToMany(Product::class, 'product_features');
    }
}
