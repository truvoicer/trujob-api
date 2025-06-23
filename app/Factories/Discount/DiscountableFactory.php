<?php

namespace App\Factories\Discount;

use App\Contracts\Discount\DiscountableInterface;
use App\Enums\Order\Discount\DiscountableType;
use App\Services\Category\CategoryDiscountableService;
use App\Services\Locale\CountryDiscountableService;
use App\Services\Locale\CurrencyDiscountableService;
use App\Services\Price\PriceDiscountableService;
use App\Services\Region\RegionDiscountableService;
use App\Services\Shipping\ShippingMethodDiscountableService;
use App\Services\Shipping\ShippingZoneDiscountableService;

class DiscountableFactory
{
    public static function create(DiscountableType $discountableType): DiscountableInterface
    {
        return match ($discountableType) {
            DiscountableType::COUNTRY => app()->make(CountryDiscountableService::class),
            DiscountableType::CURRENCY => app()->make(CurrencyDiscountableService::class),
            DiscountableType::REGION => app()->make(RegionDiscountableService::class),
            DiscountableType::CATEGORY => app()->make(CategoryDiscountableService::class),
            DiscountableType::PRICE => app()->make(PriceDiscountableService::class),
            DiscountableType::SHIPPING_METHOD => app()->make(ShippingMethodDiscountableService::class),
            DiscountableType::SHIPPING_ZONE => app()->make(ShippingZoneDiscountableService::class),
        };
    }
}
