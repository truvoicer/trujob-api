<?php
namespace App\Enums;

enum ListingType: string
{
    case EVENT = 'event';
    case VEHICLE = 'vehicle';
    case SERVICE = 'service';
    case REAL_ESTATE = 'real-estate';
    case JOB = 'job';
    case PET = 'pet';
    case ITEM = 'item';
    case PROPERTY = 'property';
    case BUSINESS = 'business';
    case TICKET = 'ticket';
    case COURSE = 'course';
    case FOOD = 'food';
}
