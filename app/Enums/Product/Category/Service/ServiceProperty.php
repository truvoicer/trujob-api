<?php

namespace App\Enums\Product\Type\Service;

enum ServiceProperty: string
{
    case TYPE = 'type';
    case WEBSITE = 'website';
    case PHONE = 'phone';
    case EMAIL = 'email';
    case OPENING_HOURS = 'opening_hours';
    case CLOSING_HOURS = 'closing_hours';
}