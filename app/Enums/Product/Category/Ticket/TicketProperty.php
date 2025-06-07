<?php

namespace App\Enums\Product\Category\Ticket;

enum TicketProperty: string
{
    case WEBSITE = 'website';
    case PHONE = 'phone';
    case EMAIL = 'email';
    case OPENING_HOURS = 'opening_hours';
    case CLOSING_HOURS = 'closing_hours';
    case TYPE = 'type';
    case START_DATE = 'start_date';
    case END_DATE = 'end_date';
    case START_TIME = 'start_time';
    case END_TIME = 'end_time';
    case SEAT_NUMBER = 'seat_number';
    case SEAT_ROW = 'seat_row';
    case SEAT_SECTION = 'seat_section';
    case SEAT = 'seat';
    case SEAT_SIDE = 'seat_side';
    case SEAT_LEVEL = 'seat_level';
    case SEAT_AREA = 'seat_area';
    case SEAT_GATE = 'seat_gate';

}