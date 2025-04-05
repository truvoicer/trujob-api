<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SidebarWidgetRole extends Model
{
    public function sidebarWidget()
    {
        return $this->belongsTo(SidebarWidget::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'sidebar_widget_roles');
    }
    
}
