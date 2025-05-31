<?php

namespace App\Enums\Order\Shipping;

use App\Models\Category;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Product;
use App\Models\Region;

enum ShippingRestrictionType: string
{
    case PRODUCT = 'product';
    case CATEGORY = 'category';
    case COUNTRY = 'country';
    case CURRENCY = 'currency';
    case REGION = 'region';

    public static function fromClassName(string $className): self
    {
        return match ($className) {
            Product::class => self::PRODUCT,
            Category::class => self::CATEGORY,
            Country::class => self::COUNTRY,
            Currency::class => self::CURRENCY,
            Region::class => self::REGION,
            default => throw new \InvalidArgumentException("Unknown class name: {$className}"),
        };
    }
}
