<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTransaction extends Model
{
    
    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function transaction() {
        return $this->belongsTo(Transaction::class);
    }
}
