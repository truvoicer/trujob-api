<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingTransaction extends Model
{
    
    public function listing() {
        return $this->belongsTo(Listing::class);
    }

    public function transaction() {
        return $this->belongsTo(Transaction::class);
    }
}
