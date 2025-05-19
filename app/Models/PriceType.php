<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PriceType extends Model
{
    protected $fillable = [
        'name',
        'label',
    ];


    public function prices()
    {
        return $this->hasMany(Price::class);
    }
}
