<?php

namespace App\Models;

use Database\Factories\messaging\MessagingGroupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessagingGroup extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id'
    ];
    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return MessagingGroupFactory::new();
    }

    public function messagingGroupMessage()
    {
        return $this->hasMany(MessagingGroupMessage::class);
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
