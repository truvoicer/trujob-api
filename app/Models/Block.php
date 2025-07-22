<?php

namespace App\Models;

use App\Enums\Block\BlockType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'type',
        'properties',
    ];
    protected $casts = [
        'type' => BlockType::class,
        'properties' => 'array'
    ];

    public function pages() {
        return $this->belongsToMany(
            Page::class,
            PageBlock::class,
        );
    }
}
