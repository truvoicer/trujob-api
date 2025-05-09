<?php

namespace App\Models;

use Database\Factories\listing\FeatureFactory;
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

    public function listings() {
        return $this->belongsToMany(Listing::class, 'listing_features');
    }
}
