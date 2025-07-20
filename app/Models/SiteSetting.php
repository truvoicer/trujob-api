<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_id',
        'locale',
        'frontend_url',
        'country_id',
        'currency_id',
        'language_id',
        'timezone',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
