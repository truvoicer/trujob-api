<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by_user_id',
        'country_id',
        'currency_id',
        'type',
        'amount',
    ];
    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

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

}
