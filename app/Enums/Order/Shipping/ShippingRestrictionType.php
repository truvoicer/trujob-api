<?php
namespace App\Enums\Order\Shipping;

enum ShippingRestrictionType: string
{
    case Product = 'product';
    case Category = 'category';
    case Location = 'location';
}