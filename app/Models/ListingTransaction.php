<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListingTransaction extends Model
{
    use HasFactory;

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }

    public function country()
    {
        return $this->hasOne(Country::class);
    }

    public function currency()
    {
        return $this->hasOne(Currency::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
