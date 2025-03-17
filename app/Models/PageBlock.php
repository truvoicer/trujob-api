<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageBlock extends Model
{
    //
    protected $fillable = [
        'properties',
        'order'
    ];

    protected $casts = [
        'properties' => 'array'
    ];

}
