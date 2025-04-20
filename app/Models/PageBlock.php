<?php

namespace App\Models;

use App\Traits\Model\PermissionTrait;
use Illuminate\Database\Eloquent\Model;

class PageBlock extends Model
{
    use PermissionTrait;
    protected $fillable = [
        'properties',
        'has_sidebar',
        'order',
        'default',
        'nav_title',
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
        'properties' => 'array',
        'has_sidebar' => 'boolean',
        'default' => 'boolean',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    public function sidebars()
    {
        return $this->belongsToMany(Sidebar::class, 'page_block_sidebars');
    }
}
