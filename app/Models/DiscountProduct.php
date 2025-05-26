<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DiscountProduct extends Model
{
    public function discountProductable(): MorphTo
    {
        return $this->morphTo();
    }
}
