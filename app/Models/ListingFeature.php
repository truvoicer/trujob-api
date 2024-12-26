<?php

namespace App\Models;

use Database\Factories\listing\ListingFeatureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingFeature extends Model
{
    use HasFactory;

    protected $fillable = [
      'label',
      'value'
    ];
    protected static function newFactory()
    {
        return ListingFeatureFactory::new();
    }
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
