<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Price extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'country_id',
        'currency_id',
        'amount',
        'valid_from',
        'valid_to',
        'is_default',
        'is_active',
    ];
    protected $casts = [
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function type()
    {
        return $this->belongsTo(PriceType::class);
    }

    public function listings()
    {
        return $this->belongsToMany(Listing::class, 'listing_prices');
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
