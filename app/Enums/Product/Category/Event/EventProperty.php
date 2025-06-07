<?php

namespace App\Enums\Product\Category\Event;

enum EventProperty: string
{
    case START_DATE = 'start_date';
    case END_DATE = 'end_date';
    case LOCATION = 'location';
    case LATITUDE = 'latitude';
    case LONGITUDE = 'longitude';
    case STATUS = 'status';
    case IS_PUBLIC = 'is_public';
    case IS_ALL_DAY = 'is_all_day';
    case IS_RECURRING = 'is_recurring';
    case NOTES = 'notes';
}