<?php

namespace App\Models;

use Database\Factories\user\UserProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dob',
        'phone',
    ];

    protected $casts = [
        'dob' => 'date',
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

}
