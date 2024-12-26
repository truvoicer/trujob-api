<?php

namespace App\Models;

use Database\Factories\user\UserMediaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'category',
        'alt',
        'url',
        'path',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return UserMediaFactory::new();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
