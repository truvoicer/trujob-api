<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;
    //

    public function sites()
    {
        return $this->belongsToMany(Site::class);
    }

    public function listings()
    {
        return $this->belongsToMany(Listing::class, 'listing_media');
    }
}
