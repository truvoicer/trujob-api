<?php

namespace App\Models;

use App\Enums\SiteStatus;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class SiteUser extends Model
{
    use HasApiTokens;

    protected $table = 'site_user';

    protected $fillable = [
        'status', // active, inactive, banned
    ];

    protected $casts = [
        'status' => SiteStatus::class,
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_site_user');
    }
    
}
