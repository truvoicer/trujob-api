<?php

namespace App\Models;

use App\Enums\BlockType;
use Illuminate\Database\Eloquent\Model;

class PageBlock extends Model
{
    //
    protected $fillable = [
        'type',
        'properties',
        'order'
    ];

    protected $casts = [
        'type' => BlockType::class,
        'properties' => 'array'
    ];

    public function page() {
        return $this->belongsTo(
            Page::class
        );
    }
}
