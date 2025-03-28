<?php

namespace App\Enums\Listing\Type\Service;

enum ServiceProperty: string
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
}