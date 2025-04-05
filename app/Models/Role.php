<?php

namespace App\Models;

use App\Repositories\RoleRepository;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    public const TABLE_NAME = 'roles';
    public const REPOSITORY = RoleRepository::class;

    protected $fillable = [
        'name',
        'label',
        'ability'
    ];
    public $timestamps = false;

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            RoleUser::TABLE_NAME,
            'role_id',
            'user_id'
        );
    }

    public function pages()
    {
        return $this->belongsToMany(Page::class);
    }

    public function pageBlocks()
    {
        return $this->belongsToMany(PageBlock::class);
    }

    public function menus()
    {
        return $this->belongsToMany(Menu::class);
    }

    public function menuItems()
    {
        return $this->belongsToMany(MenuItem::class);
    }

    public function siteUsers()
    {
        return $this->belongsToMany(SiteUser::class);
    }

    public function sidebar()
    {
        return $this->belongsToMany(Sidebar::class, SidebarRole::class);
    }
    
    public function sidebarWidgets()
    {
        return $this->belongsToMany(SidebarWidget::class, SidebarWidgetRole::class);
    }
}
