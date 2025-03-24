<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'site_id',
        'page_id',
        'active',
        'title',
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
    ];

    public function menu() {
        return $this->belongsTo(
            Menu::class
        );
    }

    public function menus() {
        return $this->hasMany(
            Menu::class
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
}
