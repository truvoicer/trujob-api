<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Discountable extends Model
{
     use HasFactory, SoftDeletes;

    protected $fillable = [
        'discount_id',
        'discountable_type',
        'discountable_id',
    ];

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function discountable(): MorphTo
    {
        return $this->morphTo();
    }
}
