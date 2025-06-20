<?php

namespace App\Factories\Shipping;

use App\Contracts\Shipping\ShippingZoneAbleInterface;
use App\Enums\Order\Shipping\ShippingZoneAbleType;
use App\Services\Category\CategoryShippingZoneAbleService;
use App\Services\Locale\CountryShippingZoneAbleService;
use App\Services\Locale\CurrencyShippingZoneAbleService;
use App\Services\Region\RegionShippingZoneAbleService;

class ShippingZoneAbleFactory
{
    public static function create(ShippingZoneAbleType $shippingZoneAbleType): ShippingZoneAbleInterface
    {
        return match ($shippingZoneAbleType) {
            ShippingZoneAbleType::COUNTRY => app()->make(CountryShippingZoneAbleService::class),
            ShippingZoneAbleType::CURRENCY => app()->make(CurrencyShippingZoneAbleService::class),
            ShippingZoneAbleType::REGION => app()->make(RegionShippingZoneAbleService::class),
            ShippingZoneAbleType::CATEGORY => app()->make(CategoryShippingZoneAbleService::class),
        };
    }
}
