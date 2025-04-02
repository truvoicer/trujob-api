<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoleSiteUser extends Model
{
    
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function siteUser()
    {
        return $this->belongsTo(SiteUser::class);
    }
}
