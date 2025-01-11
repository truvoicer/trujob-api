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
        'show_in_menu',
        'icon',
    ];

    public function appMenu() {
        return $this->belongsTo(AppMenu::class);
    }

}
