<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(
            Role::class,
            RoleUser::TABLE_NAME,
            'user_id',
            'role_id'
        );
    }


    public function userProfile()
    {
        return $this->hasOne(UserProfile::class);
    }
    public function userReward()
    {
        return $this->hasMany(UserReward::class);
    }
    public function userReview()
    {
        return $this->hasMany(UserReview::class);
    }

    public function userFollow()
    {
        return $this->hasMany(UserFollow::class);
    }

    public function listing()
    {
        return $this->hasMany(Listing::class);
    }

    public function listingFollow()
    {
        return $this->hasMany(ListingFollow::class);
    }

    public function listingTransaction()
    {
        return $this->hasMany(ListingPrice::class);
    }
    public function messagingGroup()
    {
        return $this->hasMany(MessagingGroup::class);
    }


    public function userSettings()
    {
        return $this->hasMany(UserSetting::class);
    }

    public function userMedia()
    {
        return $this->hasMany(UserMedia::class);
    }
}
