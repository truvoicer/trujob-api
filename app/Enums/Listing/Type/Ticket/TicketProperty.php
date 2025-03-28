<?php

namespace App\Enums\Listing\Type\Ticket;

enum TicketProperty: string
{
    case LOCATION = 'location';
    case LATITUDE = 'latitude';
    case LONGITUDE = 'longitude';
    case WEBSITE = 'website';
    case PHONE = 'phone';
    case EMAIL = 'email';
    case OPENING_HOURS = 'opening_hours';
    case CLOSING_HOURS = 'closing_hours';
    case CATEGORIES = 'categories';
    case FEATURES = 'features';
    case TAGS = 'tags';
    case STATUS = 'status';
    case TICKET_TYPE = 'ticket_type';
    case TICKET_PRICE = 'ticket_price';
    case TICKET_QUANTITY = 'ticket_quantity';
    case TICKET_START_DATE = 'ticket_start_date';
    case TICKET_END_DATE = 'ticket_end_date';
    case TICKET_START_TIME = 'ticket_start_time';
    case TICKET_END_TIME = 'ticket_end_time';
    case TICKET_SEAT_NUMBER = 'ticket_seat_number';
    case TICKET_SEAT_ROW = 'ticket_seat_row';
    case TICKET_SEAT_SECTION = 'ticket_seat_section';
    case TICKET_SEAT = 'ticket_seat';
    case TICKET_SEAT_SIDE = 'ticket_seat_side';
    case TICKET_SEAT_LEVEL = 'ticket_seat_level';
    case TICKET_SEAT_AREA = 'ticket_seat_area';
    case TICKET_SEAT_GATE = 'ticket_seat_gate';

}