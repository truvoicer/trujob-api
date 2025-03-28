<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Event extends Model
{
    
    public function comments(): MorphMany
    {
        return $this->morphMany(Ticket::class, 'ticketable');
    }
}
