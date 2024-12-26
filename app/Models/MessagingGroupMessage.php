<?php

namespace App\Models;

use Database\Factories\messaging\MessagingGroupMessageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessagingGroupMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'messaging_group_id',
        'message'
    ];
    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return MessagingGroupMessageFactory::new();
    }

    public function messagingGroup()
    {
        return $this->belongsTo(MessagingGroup::class);
    }
}
