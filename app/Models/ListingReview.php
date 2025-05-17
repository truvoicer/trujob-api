<?php

namespace App\Models;

use Database\Factories\listing\ListingReviewFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingReview extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'listing_id',
        'rating',
        'review',
    ];
    protected static function newFactory()
    {
        return ListingReviewFactory::new();
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
