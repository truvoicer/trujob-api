<?php

namespace App\Models;

use Database\Factories\user\UserProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'rating',
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
}
