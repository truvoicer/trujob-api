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
$currency = $country->currency()->where('code', 'GBP')->first();
if (!$currency) {
    throw new Exception('Required currency not found.');
}
return [
    [
        'label' => 'UK VAT Tax',
        'type' => TaxRateType::VAT->value,
        'amount_type' => TaxRateAmountType::PERCENTAGE->value,
        'rate' => 20.00,
        'currency_id' => $currency->id,
        'scope' => TaxScope::ORDER->value,
        'is_active' => true,
        'is_default' => true,
        'tax_rateables' => [
            [
                'tax_rateable_type' => MorphEntity::COUNTRY->value,
                'tax_rateable_id' => $country->id,
            ],
        ]
    ],
    [
        'label' => 'GBP VAT Tax',
        'type' => TaxRateType::SALES_TAX->value,
        'amount_type' => TaxRateAmountType::PERCENTAGE->value,
        'rate' => 20.00,
        'currency_id' => $currency->id,
        'scope' => TaxScope::ORDER->value,
        'is_active' => true,
        'tax_rateables' => [
            [
                'tax_rateable_type' => MorphEntity::CURRENCY->value,
                'tax_rateable_id' => $currency->id,
            ],
        ]
    ],
];
