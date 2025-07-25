<?php

namespace App\Models;

use App\Models\Traits\Orderable;
use Illuminate\Database\Eloquent\Model;

class PageBlockSidebar extends Model
{
    use Orderable;

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
