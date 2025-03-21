<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageBlock extends Model
{
    //
    protected $fillable = [
        'properties',
        'order',
        'title',
        'subtitle',
        'background_image',
        'background_color',
        'pagination',
        'pagination_type',
        'pagination_scroll_type',
        'content',
    ];

    protected $casts = [
        'pagination' => 'boolean',
        'properties' => 'array'
    ];

}
