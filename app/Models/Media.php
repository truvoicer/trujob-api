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

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_media');
    }
}
