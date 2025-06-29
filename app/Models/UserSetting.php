<?php

namespace App\Models;

use Database\Factories\user\UserSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'currency_id',
        'country_id',
        'language_id',
        'timezone',
        'app_theme',
        'push_notification'
    ];

    protected $casts = [
        'push_notification' => 'boolean',
    ];


    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserSettingFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
