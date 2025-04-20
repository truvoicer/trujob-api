<?php

namespace App\Models;

use App\Traits\Model\PermissionTrait;
use Illuminate\Database\Eloquent\Model;

class SidebarWidgetRole extends Model
{
    use PermissionTrait;
    
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
