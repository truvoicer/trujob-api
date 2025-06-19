<?php

namespace App\Factories\Tax;

use App\Contracts\Tax\TaxRateLocaleInterface;
use App\Enums\Order\Tax\TaxRateLocaleType;
use App\Services\Category\CategoryTaxRateLocaleService;
use App\Services\Locale\CountryTaxRateLocaleService;
use App\Services\Locale\CurrencyTaxRateLocaleService;
use App\Services\Region\RegionTaxRateLocaleService;

class TaxRateLocaleFactory
{
    public static function create(TaxRateLocaleType $taxRateLocaleType): TaxRateLocaleInterface
    {
        return match ($taxRateLocaleType) {
            TaxRateLocaleType::COUNTRY => app()->make(CountryTaxRateLocaleService::class),
            TaxRateLocaleType::CURRENCY => app()->make(CurrencyTaxRateLocaleService::class),
            TaxRateLocaleType::REGION => app()->make(RegionTaxRateLocaleService::class),
            TaxRateLocaleType::CATEGORY => app()->make(CategoryTaxRateLocaleService::class),
        };
    }
}
