<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;
    protected $fillable = [
        'country_id',
        'name',
        'name_plural',
        'code',
        'symbol',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function user()
    {
        return $this->belongsTo(UserProfile::class);
    }

    public function productTransaction()
    {
        return $this->belongsTo(ProductPrice::class);
    }

    public function taxRateLocales()
    {
        return $this->morphMany(TaxRateLocale::class, 'localeable');
    }

}
