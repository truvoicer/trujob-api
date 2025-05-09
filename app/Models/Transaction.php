<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    
    public function listings() {
        return $this->belongsToMany(Listing::class, 'listing_transactions');
    }
    
    public function listing() {
        return $this->belongsTo(Listing::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function price() {
        return $this->belongsTo(Price::class);
    }
    
    public function currency() {
        return $this->belongsTo(Currency::class);
    }
}
