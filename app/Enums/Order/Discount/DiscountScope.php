<?php
namespace App\Enums\Order\Discount;

enum DiscountScope: string
{
    case GLOBAL = 'global';
    case ORDER = 'order';
    case PRODUCT = 'product';
    case CATEGORY = 'category';
    case SHIPPING = 'shipping';
}