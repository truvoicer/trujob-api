<?php

namespace App\Models;

use App\Enums\Block\PageBlockType;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $fillable = [
        'type',
        'properties',
    ];
    protected $casts = [
        'type' => PageBlockType::class,
        'properties' => 'array'
    ];

    public function pages() {
        return $this->belongsToMany(
            Page::class,
            PageBlock::class,
        );
    }
}
