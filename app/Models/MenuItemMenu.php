<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItemMenu extends Model
{
    use HasFactory;

    protected $fillable = [
        'menu_item_id',
        'menu_id',
        'order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function menuItem() {
        return $this->belongsTo(
            MenuItem::class,
        );
    }

    public function menu() {
        return $this->belongsTo(
            Menu::class,
        );
    }
}
