<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Enums\SiteStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Laravel\Sanctum\HasApiTokens;

class SiteUser extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'site_user';

    protected $fillable = [
        'status', // active, inactive, banned
        'password_reset',
        'site_id',
        'user_id',
    ];

    protected $casts = [
        'status' => SiteStatus::class,
        'password_reset' => 'boolean',
    ];

    public function siteUserable(): MorphTo
    {
        return $this->morphTo();
    }

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
