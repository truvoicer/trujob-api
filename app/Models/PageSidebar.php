<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSidebar extends Model
{
    protected $fillable = [
        'order',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'order' => 'integer',
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
