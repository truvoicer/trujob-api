<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'iso2',
        'iso3',
        'phone_code'
    ];


    public function user()
    {
        return $this->belongsTo(UserProfile::class);
    }

    public function currency()
    {
        return $this->hasOne(Currency::class);
    }

    public function listingTransaction()
    {
        return $this->belongsTo(ListingTransaction::class);
    }
}
