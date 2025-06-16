<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\user\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\user\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'first_name',
        'last_name',
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

    protected static function newFactory()
    {
        return UserFactory::new();
    }

    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
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

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productFollow()
    {
        return $this->hasMany(ProductFollow::class);
    }

    public function productTransaction()
    {
        return $this->hasMany(ProductPrice::class);
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

    public function sites()
    {
        return $this->belongsToMany(Site::class)
            ->withPivot('status', 'id')
            ->withTimestamps();
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function discountUsages()
    {
        return $this->hasMany(UserDiscountUsage::class);
    }

    public function siteUsers(): MorphMany
    {
        return $this->morphMany(SiteUser::class, 'siteUserable');
    }

    public function hasReachedDiscountLimit(Discount $discount): bool
    {
        if ($discount->per_user_limit === null) {
            return false;
        }

        $usage = $this->discountUsages()->firstOrCreate(
            ['discount_id' => $discount->id],
            ['usage_count' => 0]
        );

        return $usage->usage_count >= $discount->per_user_limit;
    }
}
