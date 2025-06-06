<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DefaultDiscount extends Model
{

    protected $table = 'default_discounts';
    
    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}
