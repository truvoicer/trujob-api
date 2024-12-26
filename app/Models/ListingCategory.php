<?php

namespace App\Models;

use Database\Factories\listing\ListingCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingCategory extends Model
{
    use HasFactory;
    protected static function newFactory() {
        return ListingCategoryFactory::new();
    }
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
