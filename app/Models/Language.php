<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'iso639_1',
        'iso639_2',
    ];

    // If you were to implement a many-to-many with countries
    public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_languages');
    }
}
