<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CountryLanguage extends Model
{
    protected $table = 'country_languages';

    protected $fillable = [
        'country_id',
        'language_id',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
