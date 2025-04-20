<?php

namespace App\Models;

use App\Traits\Model\PermissionTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory, PermissionTrait;

    protected $fillable = [
        'site_id',
        'name',
        'ul_class',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function hasParent() {
        return $this->menuItem !== null;
    }

    public function menuItems() {
        return $this->belongsToMany(
            MenuItem::class,
            'menu_menu_items',
        );
    }

    public function site() {
        return $this->belongsTo(
            Site::class
        );
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

}
