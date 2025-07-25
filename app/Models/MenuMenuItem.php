<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuMenuItem extends Model
{
    protected $table = 'menu_menu_items';

    protected $fillable = [
        'menu_id',
        'menu_item_id',
        'order',
        'active',
    ];
}
