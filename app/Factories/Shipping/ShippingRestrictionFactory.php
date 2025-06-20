<?php

namespace App\Factories\Shipping;

use App\Contracts\Shipping\ShippingRestriction;
use App\Enums\Order\Shipping\ShippingRestrictionType;
use App\Services\Category\CategoryShippingRestrictionService;
use App\Services\Product\ProductShippingRestrictionService;
use App\Services\Locale\CountryShippingRestrictionService;
use App\Services\Locale\CurrencyShippingRestrictionService;
use App\Services\Region\RegionShippingRestrictionService;

class ShippingRestrictionFactory
{
    public static function create(ShippingRestrictionType $shippingRestrictionType): ShippingRestriction
    {
        return match ($shippingRestrictionType) {
            ShippingRestrictionType::PRODUCT => app()->make(ProductShippingRestrictionService::class),
            ShippingRestrictionType::COUNTRY => app()->make(CountryShippingRestrictionService::class),
            ShippingRestrictionType::CURRENCY => app()->make(CurrencyShippingRestrictionService::class),
            ShippingRestrictionType::REGION => app()->make(RegionShippingRestrictionService::class),
            ShippingRestrictionType::CATEGORY => app()->make(CategoryShippingRestrictionService::class),
        };
    }
}
