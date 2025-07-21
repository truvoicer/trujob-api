<?php

namespace App\Models;

use App\Traits\Model\PermissionTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SidebarWidget extends Model
{
    use PermissionTrait, HasFactory;

    protected $fillable = [
        'title',
        'icon',
        'order',
        'has_container',
        'properties',
        'sidebar_id',
        'widget_id',
    ];
    protected $casts = [
        'properties' => 'array',
        'has_container' => 'boolean',
    ];
    public function sidebar()
    {
        return $this->belongsTo(Sidebar::class);
    }
    public function widget()
    {
        return $this->belongsTo(Widget::class);
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'sidebar_widget_roles');
    }
}
