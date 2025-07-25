<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppMenuItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'initial_screen',
        'label',
        'screen',
        'type',
        'active',
        'show_in_menu',
        'icon',
    ];

    protected $casts = [
        'active' => 'boolean',
        'show_in_menu' => 'boolean',
    ];

    public function appMenu() {
        return $this->belongsToMany(
            AppMenu::class,
            'app_menu_app_menu_items'
        );
    }

}
