<?php
namespace App\Enums\Auth;

enum ApiTokenExpiry: string
{
    case ONE_HOUR = '+1 hour';
    case ONE_DAY = '+1 day';
    case ONE_WEEK = '+1 week';
    case ONE_MONTH = '+1 month';
    case ONE_YEAR = '+1 year';
    case NEVER = 'never';
}
