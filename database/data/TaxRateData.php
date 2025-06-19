<?php


use App\Enums\MorphEntity;
use App\Enums\Order\Tax\TaxRateAmountType;
use App\Enums\Order\Tax\TaxRateType;
use App\Enums\Order\Tax\TaxScope;
use App\Models\Country;
use App\Models\Currency;

$country = Country::where('iso2', 'GB')->first();
if (!$country) {
    throw new Exception('Required country not found.');
}
$currency = Currency::where('code', 'GBP')->first();
if (!$currency) {
    throw new Exception('Required currency not found.');
}
return [
    [
        'label' => 'Tax Ratesa',
        'type' => TaxRateType::VAT->value,
        'amount_type' => TaxRateAmountType::PERCENTAGE->value,
        'rate' => 20.00,
        'currency_id' => $currency->id,
        'scope' => TaxScope::ORDER->value,
        'is_active' => true,
        'locales' => [
            [
                'localeable_type' => MorphEntity::COUNTRY->value,
                'localeable_id' => $country->id,
            ],
        ]
    ]
];
