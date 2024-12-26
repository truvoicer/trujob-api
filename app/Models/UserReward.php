<?php

namespace App\Models;

use Database\Factories\user\UserRewardFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReward extends Model
{
    use HasFactory;


    public $timestamps = false;
    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserRewardFactory::new();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
