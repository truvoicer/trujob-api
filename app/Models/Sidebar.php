<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sidebar extends Model
{
    //
    protected $fillable = [
        'name',
        'title',
        'icon',
    ];
    protected $casts = [
        'properties' => 'array',
    ];

    public function widgets()
    {
        return $this->hasMany(SidebarWidget::class);
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'sidebar_role');
    }
    public function pageBlocks()
    {
        return $this->belongsToMany(PageBlock::class);
    }
}
