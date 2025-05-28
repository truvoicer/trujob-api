<?php
namespace App\Enums\Order\Shipping;

enum ShippingRestrictionType: string
{
    case LISTING = 'listing';
    case CATEGORY = 'category';
    case COUNTRY = 'country';
    case CURRENCY = 'currency';
    case REGION = 'region';
}