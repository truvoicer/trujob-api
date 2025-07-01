<?php

use App\Enums\Order\Shipping\ShippingRateType;
use App\Enums\Order\Shipping\ShippingRestrictionAction;
use App\Enums\Order\Shipping\ShippingRestrictionType;
use App\Enums\Order\Shipping\ShippingUnit;
use App\Enums\Order\Shipping\ShippingWeightUnit;
use App\Models\Country;
use App\Models\Currency;
use App\Models\ShippingZone;

$country = Country::where('iso2', 'GB')->first();
if (!$country) {
    throw new Exception('Required country not found.');
}
$currency = $country->currency()->where('code', 'GBP')->first();
if (!$currency) {
    throw new Exception('Required currency not found.');
}
$domesticShippingZone = ShippingZone::where('name', 'domestic')->first();
if (!$domesticShippingZone) {
    throw new Exception('Required shipping zone not found.');
}
$internationalShippingZone = ShippingZone::where('name', 'international')->first();
if (!$internationalShippingZone) {
    throw new Exception('Required shipping zone not found.');
}
$restrictionCountry = Country::where('iso2', 'CN')->first();
if (!$restrictionCountry) {
    throw new Exception('Required country not found.');
}
return [
    [
        'label' => 'Domestic Shipping',
        'description' => 'Delivery within 5-7 business days',
        'is_active' => true,
        'processing_time_days' => 2,
        'rates' => [
            [
                'shipping_zone_id' => $domesticShippingZone->id,
                'type' => ShippingRateType::FREE->value,
                'weight_limit' => true,
                'height_limit' => true,
                'width_limit' => true,
                'length_limit' => true,
                'weight_unit' => ShippingWeightUnit::G->value,
                'height_unit' => ShippingUnit::CM->value,
                'width_unit' => ShippingUnit::CM->value,
                'length_unit' => ShippingUnit::CM->value,
                'min_weight' => 0,
                'max_weight' => 200,
                'min_height' => 0,
                'max_height' => 30,
                'min_width' => 0,
                'max_width' => 20,
                'min_length' => 0,
                'max_length' => 10,
                'is_active' => true
            ],
            [
                'shipping_zone_id' => $domesticShippingZone->id,
                'type' => ShippingRateType::FLAT_RATE->value,
                'weight_limit' => true,
                'height_limit' => true,
                'width_limit' => true,
                'length_limit' => true,
                'weight_unit' => ShippingWeightUnit::G->value,
                'height_unit' => ShippingUnit::CM->value,
                'width_unit' => ShippingUnit::CM->value,
                'length_unit' => ShippingUnit::CM->value,
                'min_weight' => 0,
                'max_weight' => 500,
                'min_height' => 0,
                'max_height' => 50,
                'min_width' => 0,
                'max_width' => 40,
                'min_length' => 0,
                'max_length' => 15,
                'amount' => 5.00, // Flat rate amount
                'currency_id' => $currency->id,
                'is_active' => true
            ],
            [
                'shipping_zone_id' => $domesticShippingZone->id,
                'type' => ShippingRateType::FLAT_RATE->value,
                'weight_limit' => true,
                'height_limit' => true,
                'width_limit' => true,
                'length_limit' => true,
                'weight_unit' => ShippingWeightUnit::KG->value,
                'height_unit' => ShippingUnit::CM->value,
                'width_unit' => ShippingUnit::CM->value,
                'length_unit' => ShippingUnit::CM->value,
                'min_weight' => 0,
                'max_weight' => 2,
                'min_height' => 0,
                'max_height' => 100,
                'min_width' => 0,
                'max_width' => 80,
                'min_length' => 0,
                'max_length' => 40,
                'amount' => 15.00, // Flat rate amount
                'currency_id' => $currency->id,
                'is_active' => true
            ],
        ],
    ],
    [
        'label' => 'International Shipping',
        'description' => 'Delivery within 5-7 business days',
        'is_active' => true,
        'processing_time_days' => 2,
        'rates' => [
            [
                'shipping_zone_id' => $internationalShippingZone->id,
                'type' => ShippingRateType::FLAT_RATE->value,
                'weight_limit' => true,
                'height_limit' => true,
                'width_limit' => true,
                'length_limit' => true,
                'weight_unit' => ShippingWeightUnit::KG->value,
                'height_unit' => ShippingUnit::CM->value,
                'width_unit' => ShippingUnit::CM->value,
                'length_unit' => ShippingUnit::CM->value,
                'min_weight' => 0,
                'max_weight' => 4,
                'min_height' => 0,
                'max_height' => 200,
                'min_width' => 0,
                'max_width' => 100,
                'min_length' => 0,
                'max_length' => 50,
                'amount' => 30.00, // Flat rate amount
                'currency_id' => $currency->id,
                'is_active' => true
            ],
        ],
        'restrictions' => [
            [
                'type' => ShippingRestrictionType::COUNTRY->value,
                'restriction_id' =>  $restrictionCountry->id,
                'action' => ShippingRestrictionAction::DENY->value,
            ],
        ]
    ],
];
