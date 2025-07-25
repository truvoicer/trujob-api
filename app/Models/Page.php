<?php

namespace App\Models;

use App\Enums\ViewType;
use App\Traits\Model\PermissionTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory, PermissionTrait;

    protected $fillable = [
        'site_id',
        'permalink',
        'view',
        'name',
        'title',
        'content',
        'is_active',
        'is_home',
        'is_featured',
        'is_protected',
        'has_sidebar',
        'settings',
    ];

    protected $casts = [
        'view' => ViewType::class,
        'settings' => 'array',
        'is_active' => 'boolean',
        'is_home' => 'boolean',
        'is_featured' => 'boolean',
        'is_protected' => 'boolean',
        'has_sidebar' => 'boolean',
    ];

    public function pageBlocks()
    {
        return $this->hasMany(PageBlock::class);
    }

    public function blocks()
    {
        return $this->belongsToMany(
            Block::class,
            PageBlock::class,
        )->withPivot(
            'order',
            'has_sidebar',
            'properties',
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
        );
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function sidebars()
    {
        return $this->belongsToMany(Sidebar::class, 'page_sidebars');
    }

    public function pageSidebars()
    {
        return $this->hasMany(PageSidebar::class);
    }
}
