<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageBlockSidebar extends Model
{
    protected $fillable = [
        'name',
        'title',
        'icon',
        'properties',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    public function pageBlock()
    {
        return $this->belongsTo(PageBlock::class);
    }
    
    public function sidebar()
    {
        return $this->belongsTo(Sidebar::class);
    }

}
