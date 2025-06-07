<?php
namespace App\Enums\Order\Shipping;

enum ShippingUnit: string
{
    case CM = 'cm';
    case INCH = 'inch';
    case METER = 'meter';
    case FEET = 'feet';
}