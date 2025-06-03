<?php

namespace App\Enums\Product\Type\Business;

enum BusinessProperty: string
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
}