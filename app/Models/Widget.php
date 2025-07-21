<?php

namespace App\Models;

use App\Traits\Model\PermissionTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Widget extends Model
{
    use PermissionTrait, HasFactory;
    
    protected $fillable = [
        'name',
        'title',
        'icon',
        'description',
        'properties',
    ];
    protected $casts = [
        'properties' => 'array',
    ];

    public function sidebarWidgets() {
        return $this->hasMany(SidebarWidget::class);
    }

    public function site() {
        return $this->belongsTo(Site::class);
    }

    public function sidebars() {
        return $this->belongsToMany(Sidebar::class, 'sidebar_widgets')->withPivot(
            'title',
            'icon',
            'order',
            'has_container',
            'properties'
        );
    }

    public function roles() {
        return $this->belongsToMany(Role::class, 'widget_roles');
    }
}
