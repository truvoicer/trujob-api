<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SidebarWidget extends Model
{
    //
    protected $fillable = [
        'title',
        'icon',
        'order',
        'has_container',
        'properties',
    ];
    protected $casts = [
        'properties' => 'array',
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
