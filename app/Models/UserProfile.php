<?php

namespace App\Models;

use Database\Factories\user\UserProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'dob',
        'phone',
        'country_id',
        'currency_id',
        'user_id',
        'language_id',
    ];


    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserProfileFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function country()
    {
        return $this->hasOne(Country::class);
    }

    public function currency()
    {
        return $this->hasOne(Country::class);
    }

    public function language()
    {
        return $this->hasOne(Language::class);
    }
}
