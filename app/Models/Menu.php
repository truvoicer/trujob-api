<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_id',
        'menu_item_id',
        'name',
        'ul_class',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function menuItems() {
        return $this->hasMany(
            MenuItem::class
        );
    }

    public function site() {
        return $this->belongsTo(
            Site::class
        );
    }

    public function menuItem() {
        return $this->belongsTo(
            MenuItem::class
        );
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

}
