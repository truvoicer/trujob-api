<?php

namespace App\Enums\Order\Discount;

use App\Models\Category;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Price;
use App\Models\Region;
use App\Models\ShippingMethod;
use App\Models\ShippingZone;

enum DiscountableType: string
{
    case CATEGORY = 'category';
    case COUNTRY = 'country';
    case CURRENCY = 'currency';
    case REGION = 'region';
    case PRICE = 'price';
    case SHIPPING_METHOD = 'shipping_method';
    case SHIPPING_ZONE = 'shipping_zone';

    public static function fromClassName(string $className): self
    {
        return match ($className) {
            Category::class => self::CATEGORY,
            Country::class => self::COUNTRY,
            Currency::class => self::CURRENCY,
            Region::class => self::REGION,
            Price::class => self::PRICE,
            ShippingMethod::class => self::SHIPPING_METHOD,
            ShippingZone::class => self::SHIPPING_ZONE,
            default => throw new \InvalidArgumentException("Unknown class name: {$className}"),
        };
    }
}
