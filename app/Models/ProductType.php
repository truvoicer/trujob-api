<?php

namespace App\Models;

use Database\Factories\listing\ProductTypeFactory;
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
}
