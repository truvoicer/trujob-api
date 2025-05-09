<?php

namespace App\Models;

use Database\Factories\listing\BrandFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'label'
    ];

    protected static function newFactory() {
        return BrandFactory::new();
    }

    public function listings() {
        return $this->belongsToMany(Listing::class, 'listing_brands');
    }
}
