<?php
namespace App\Enums\Order\Shipping;

enum ShippingRestrictionAction: string
{
    case ALLOW = 'allow';
    case DENY = 'deny';
}