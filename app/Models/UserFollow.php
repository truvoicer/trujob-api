<?php

namespace App\Models;

use Database\Factories\user\UserFollowFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFollow extends Model
{
    use HasFactory;
    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserFollowFactory::new();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function follower()
    {
        return $this->belongsTo(User::class, 'follow_user_id', 'id');
    }
}
