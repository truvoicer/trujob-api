<?php

namespace App\Models;

use App\Enums\ViewType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'view',
        'slug',
        'title',
        'content',
        'is_active',
        'is_home',
        'is_featured',
        'is_protected',
        'settings',
    ];

    protected $casts = [
        'view' => ViewType::class,
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_home' => 'boolean',
        'is_featured' => 'boolean',
    ];

    public function blocks()
    {
        return $this->belongsToMany(
            Block::class,
            PageBlock::class,
        )->withPivot(
            'order',
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
        );
    }
}
