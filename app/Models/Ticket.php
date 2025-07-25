<?php

namespace App\Models;

use App\Enums\Product\Category\Ticket\TicketProperty;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'created_by_user_id',
        TicketProperty::WEBSITE,
        TicketProperty::PHONE,
        TicketProperty::EMAIL,
        TicketProperty::OPENING_HOURS,
        TicketProperty::CLOSING_HOURS,
        TicketProperty::TYPE,
        TicketProperty::START_DATE,
        TicketProperty::END_DATE,
        TicketProperty::START_TIME,
        TicketProperty::END_TIME,
        TicketProperty::SEAT_NUMBER,
        TicketProperty::SEAT_ROW,
        TicketProperty::SEAT_SECTION,
        TicketProperty::SEAT,
        TicketProperty::SEAT_SIDE,
        TicketProperty::SEAT_LEVEL,
        TicketProperty::SEAT_GATE,
        TicketProperty::SEAT_AREA
    ];

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
