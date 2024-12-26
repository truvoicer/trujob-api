<?php

namespace App\Models;

use Database\Factories\listing\ListingFollowFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingFollow extends Model
{
    use HasFactory;

    protected static function newFactory()
    {
        return ListingFollowFactory::new();
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
