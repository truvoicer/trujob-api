<?php

namespace App\Models;

use App\Enums\MenuItemType;
use App\Helpers\MenuHelpers;
use App\Traits\Model\PermissionTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory, PermissionTrait;
    protected $fillable = [
        'site_id',
        'page_id',
        'active',
        'label',
        'type',
        'url',
        'target',
        'order',
        'icon',
        'li_class',
        'a_class',
    ];

    protected $casts = [
        'active' => 'boolean',
        'type' => MenuItemType::class,
    ];

    protected function url (): Attribute
    {
        return Attribute::make(
            get: fn ($value) => MenuHelpers::getMenuItemUrl($this->type, $value, $this->page),
        );
    }
    

    public function menus() {
        return $this->belongsToMany(
            Menu::class,
            'menu_item_menus',
        );
    }

    public function page() {
        return $this->belongsTo(
            Page::class
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
