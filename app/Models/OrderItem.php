<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderItem extends Model
{
    
    public function orderItemable(): MorphTo
    {
        return $this->morphTo();
    }
}
