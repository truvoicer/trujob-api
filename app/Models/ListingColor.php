<?php

namespace App\Models;

use Database\Factories\listing\ListingColorFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingColor extends Model
{
    use HasFactory;
    protected static function newFactory() {
        return ListingColorFactory::new();
    }
    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
    public function color()
    {
        return $this->belongsTo(Color::class);
    }
}
