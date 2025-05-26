<?php

namespace App\Models;

use Database\Factories\listing\CategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'label'
    ];
    protected static function newFactory() {
        return CategoryFactory::new();
    }

    public function listings() {
        return $this->belongsToMany(Listing::class, 'listing_categories');
    }

    public function discounts()
{
    return $this->belongsToMany(Discount::class)
        ->withTimestamps();
}
}
