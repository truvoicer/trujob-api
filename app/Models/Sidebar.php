<?php

namespace App\Models;

use App\Traits\Model\PermissionTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sidebar extends Model
{
    use PermissionTrait, HasFactory;
    
    protected $fillable = [
        'name',
        'title',
        'icon',
    ];
    protected $casts = [
        'properties' => 'array',
    ];

    public function sidebarWidgets()
    {
        return $this->hasMany(SidebarWidget::class);
    }

    public function widgets()
    {
        return $this->belongsToMany(Widget::class, 'sidebar_widgets')->withPivot(
            'title',
            'icon',
            'order',
            'has_container',
            'properties'
        );
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'sidebar_role');
    }
    public function pageBlocks()
    {
        return $this->belongsToMany(PageBlock::class, 'page_block_sidebars');
    }

    public function pages()
    {
        return $this->belongsToMany(Page::class, 'page_sidebars');
    }
}
