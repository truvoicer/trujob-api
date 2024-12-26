<?php

namespace App\Models;

use Database\Factories\user\UserReviewFactory;
use Database\Factories\user\UserRewardFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserReview extends Model
{
    use HasFactory;


    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserReviewFactory::new();
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
