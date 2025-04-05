<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SidebarRole extends Model
{
    //
    public function sidebar()
    {
        return $this->belongsTo(Sidebar::class);
    }
    
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
