<?php

namespace App\Models;

use Database\Factories\user\UserRewardFactory;
use Database\Factories\user\UserSettingFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    use HasFactory;


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
}
