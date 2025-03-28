<?php

namespace App\Enums\Listing\Type\Event;

enum EventProperty: string
{
    case START_DATE = 'start_date';
    case END_DATE = 'end_date';
    case START_TIME = 'start_time';
    case END_TIME = 'end_time';
    case LOCATION = 'location';
    case LATITUDE = 'latitude';
    case LONGITUDE = 'longitude';
    case TICKET_PRICE = 'ticket_price';
    case TICKET_QUANTITY = 'ticket_quantity';
    case TICKET_SALES_START = 'ticket_sales_start';
    case TICKET_SALES_END = 'ticket_sales_end';
    case TICKET_SALES_ENABLED = 'ticket_sales_enabled';
    case TICKET_SALES_TYPE = 'ticket_sales_type';
    case TICKET_SALES_TYPE_FIXED = 'fixed';
    case TICKET_SALES_TYPE_FREE = 'free';
    case TICKET_SALES_TYPE_DONATION = 'donation';
    case TICKET_SALES_TYPE_CUSTOM = 'custom';
}