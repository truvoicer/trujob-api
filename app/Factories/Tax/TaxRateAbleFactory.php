<?php

namespace App\Factories\Tax;

use App\Contracts\Tax\TaxRateAbleInterface;
use App\Enums\Order\Tax\TaxRateAbleType;
use App\Services\Category\CategoryTaxRateAbleService;
use App\Services\Locale\CountryTaxRateAbleService;
use App\Services\Locale\CurrencyTaxRateAbleService;
use App\Services\Region\RegionTaxRateAbleService;

class TaxRateAbleFactory
{
    public static function create(TaxRateAbleType $taxRateAbleType): TaxRateAbleInterface
    {
        return match ($taxRateAbleType) {
            TaxRateAbleType::COUNTRY => app()->make(CountryTaxRateAbleService::class),
            TaxRateAbleType::CURRENCY => app()->make(CurrencyTaxRateAbleService::class),
            TaxRateAbleType::REGION => app()->make(RegionTaxRateAbleService::class),
            TaxRateAbleType::CATEGORY => app()->make(CategoryTaxRateAbleService::class),
        };
    }
}
