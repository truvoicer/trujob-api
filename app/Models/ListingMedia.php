<?php

namespace App\Models;

use Database\Factories\listing\ListingMediaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'category',
        'alt',
        'url',
        'path',
        'filesystem'
    ];

    protected static function newFactory()
    {
        return ListingMediaFactory::new();
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
}
