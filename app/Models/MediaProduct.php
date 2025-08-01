<?php

namespace App\Models;

use Database\Factories\product\MediaOrderItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'category',
        'alt',
        'url',
        'path',
        'filesystem'
    ];

    protected static function newFactory()
    {
        return MediaOrderItemFactory::new();
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
