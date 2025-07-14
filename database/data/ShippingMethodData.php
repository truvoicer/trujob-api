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
$currency = Currency::where('code', 'GBP')->first();
if (!$currency) {
    throw new Exception('Required currency not found.');
}
$domesticShippingZone = ShippingZone::where('name', 'domestic')->first();
if (!$domesticShippingZone) {
    throw new Exception('Required shipping zone not found.');
}


$internationalCountry = Country::where('iso2', 'JP')->first();
if (!$internationalCountry) {
    throw new Exception('Required country not found.');
}
$internationalCurrency = Currency::where('code', 'JPY')->first();
if (!$internationalCurrency) {
    throw new Exception('Required currency not found.');
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
                'label' => 'Small parcel',
                'description' => 'A small parcel up to 2kg',
                'shipping_zone_id' => $domesticShippingZone->id,
                'type' => ShippingRateType::DIMENSION_BASED->value,
                'has_max_dimension' => true,
                'max_dimension' => 90,
                'max_dimension_unit' => ShippingUnit::CM->value,
                'has_weight' => true,
                'has_height' => false,
                'has_width' => false,
                'has_depth' => false,
                'weight_unit' => ShippingWeightUnit::KG->value,
                'height_unit' => ShippingUnit::CM->value,
                'width_unit' => ShippingUnit::CM->value,
                'depth_unit' => ShippingUnit::CM->value,
                'max_weight' => 2,
                'max_height' => null,
                'max_width' => null,
                'max_depth' => null,
                'is_active' => true,
                'amount' => 5.00, // Flat rate amount
                'currency_id' => $currency->id,
                'dimensional_weight_divisor' => 50,
            ],
            [
                'label' => 'Medium parcel',
                'description' => 'A medium parcel with dimensions up to 61x61x46 cm and weight up to 2 kg',
                'shipping_zone_id' => $domesticShippingZone->id,
                'type' => ShippingRateType::DIMENSION_BASED->value,
                'has_max_dimension' => false,
                'max_dimension' => null,
                'max_dimension_unit' => null,
                'has_weight' => true,
                'has_height' => true,
                'has_width' => true,
                'has_depth' => true,
                'weight_unit' => ShippingWeightUnit::KG->value,
                'height_unit' => ShippingUnit::CM->value,
                'width_unit' => ShippingUnit::CM->value,
                'depth_unit' => ShippingUnit::CM->value,
                'max_weight' => 2,
                'max_height' => 61,
                'max_width' => 61,
                'max_depth' => 46,
                'is_active' => true,
                'amount' => 7.00, // Flat rate amount
                'currency_id' => $currency->id,
                'dimensional_weight_divisor' => 50,
            ],
            [
                'label' => 'Medium parcel up to 7kg',
                'description' => 'A medium parcel with a maximum weight of 7kg',
                'shipping_zone_id' => $domesticShippingZone->id,
                'type' => ShippingRateType::DIMENSION_BASED->value,
                'has_max_dimension' => false,
                'max_dimension' => null,
                'max_dimension_unit' => null,
                'has_weight' => true,
                'has_height' => true,
                'has_width' => true,
                'has_depth' => true,
                'weight_unit' => ShippingWeightUnit::KG->value,
                'height_unit' => ShippingUnit::CM->value,
                'width_unit' => ShippingUnit::CM->value,
                'depth_unit' => ShippingUnit::CM->value,
                'max_weight' => 7,
                'max_height' => 61,
                'max_width' => 61,
                'max_depth' => 46,
                'is_active' => true,
                'amount' => 12.00, // Flat rate amount
                'currency_id' => $currency->id,
                'dimensional_weight_divisor' => 50,
            ],
            [
                'label' => 'large parcel up to 17kg',
                'description' => 'A large parcel with a maximum weight of 17kg',
                'shipping_zone_id' => $domesticShippingZone->id,
                'type' => ShippingRateType::FLAT_RATE->value,
                'has_max_dimension' => false,
                'max_dimension' => null,
                'max_dimension_unit' => null,
                'has_weight' => true,
                'has_height' => true,
                'has_width' => true,
                'has_depth' => true,
                'weight_unit' => ShippingWeightUnit::KG->value,
                'height_unit' => ShippingUnit::CM->value,
                'width_unit' => ShippingUnit::CM->value,
                'depth_unit' => ShippingUnit::CM->value,
                'max_weight' => 17,
                'max_height' => 61,
                'max_width' => 61,
                'max_depth' => 46,
                'is_active' => true,
                'amount' => 12.00, // Flat rate amount
                'currency_id' => $currency->id,
                'dimensional_weight_divisor' => 50,
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
                'label' => 'Free shipping under 500 grams',
                'description' => 'Free shipping for orders under 500 grams',
                'shipping_zone_id' => $internationalShippingZone->id,
                'type' => ShippingRateType::FREE->value,
                'has_max_dimension' => false,
                'max_dimension' => null,
                'max_dimension_unit' => null,
                'has_weight' => true,
                'has_height' => true,
                'has_width' => true,
                'has_depth' => true,
                'weight_unit' => ShippingWeightUnit::G->value,
                'height_unit' => ShippingUnit::CM->value,
                'width_unit' => ShippingUnit::CM->value,
                'depth_unit' => ShippingUnit::CM->value,
                'max_weight' => 500,
                'max_height' => 61,
                'max_width' => 61,
                'max_depth' => 46,
                'is_active' => true,
                'amount' => null,
                'currency_id' => null,
                'dimensional_weight_divisor' => 50,
            ],
            [
                'label' => 'Small parcel',
                'description' => 'A small parcel up to 1kg',
                'shipping_zone_id' => $internationalShippingZone->id,
                'type' => ShippingRateType::WEIGHT_BASED->value,
                'has_max_dimension' => true,
                'max_dimension' => 90,
                'max_dimension_unit' => ShippingUnit::CM->value,
                'has_weight' => true,
                'has_height' => false,
                'has_width' => false,
                'has_depth' => false,
                'weight_unit' => ShippingWeightUnit::KG->value,
                'height_unit' => ShippingUnit::CM->value,
                'width_unit' => ShippingUnit::CM->value,
                'depth_unit' => ShippingUnit::CM->value,
                'max_weight' => 1,
                'max_height' => null,
                'max_width' => null,
                'max_depth' => null,
                'is_active' => true,
                'amount' => 25.00, // Flat rate amount
                'currency_id' => $internationalCurrency->id,
                'dimensional_weight_divisor' => 50,
            ],
            [
                'label' => 'Medium parcel',
                'description' => 'A medium parcel with dimensions up to 61x61x46 cm and weight up to 10 kg',
                'shipping_zone_id' => $internationalShippingZone->id,
                'type' => ShippingRateType::DIMENSION_BASED->value,
                'has_max_dimension' => false,
                'max_dimension' => null,
                'max_dimension_unit' => null,
                'has_weight' => true,
                'has_height' => true,
                'has_width' => true,
                'has_depth' => true,
                'weight_unit' => ShippingWeightUnit::KG->value,
                'height_unit' => ShippingUnit::CM->value,
                'width_unit' => ShippingUnit::CM->value,
                'depth_unit' => ShippingUnit::CM->value,
                'max_weight' => 10,
                'max_height' => 61,
                'max_width' => 61,
                'max_depth' => 46,
                'is_active' => true,
                'amount' => 57.00, // Flat rate amount
                'currency_id' => $internationalCurrency->id,
                'dimensional_weight_divisor' => 50,
            ],
            [
                'label' => 'Medium parcel up to 7kg',
                'description' => 'A medium parcel with a maximum weight of 7kg',
                'shipping_zone_id' => $internationalShippingZone->id,
                'type' => ShippingRateType::DIMENSION_BASED->value,
                'has_max_dimension' => false,
                'max_dimension' => null,
                'max_dimension_unit' => null,
                'has_weight' => true,
                'has_height' => true,
                'has_width' => true,
                'has_depth' => true,
                'weight_unit' => ShippingWeightUnit::KG->value,
                'height_unit' => ShippingUnit::CM->value,
                'width_unit' => ShippingUnit::CM->value,
                'depth_unit' => ShippingUnit::CM->value,
                'max_weight' => 7,
                'max_height' => 61,
                'max_width' => 61,
                'max_depth' => 46,
                'is_active' => true,
                'amount' => 112.00, // Flat rate amount
                'currency_id' => $internationalCurrency->id,
                'dimensional_weight_divisor' => 50,
            ],
            [
                'label' => 'Large parcel up to 17kg',
                'description' => 'A large parcel with a maximum weight of 17kg',
                'shipping_zone_id' => $internationalShippingZone->id,
                'type' => ShippingRateType::FLAT_RATE->value,
                'has_max_dimension' => false,
                'max_dimension' => null,
                'max_dimension_unit' => null,
                'has_weight' => true,
                'has_height' => true,
                'has_width' => true,
                'has_depth' => true,
                'weight_unit' => ShippingWeightUnit::KG->value,
                'height_unit' => ShippingUnit::CM->value,
                'width_unit' => ShippingUnit::CM->value,
                'depth_unit' => ShippingUnit::CM->value,
                'max_weight' => 17,
                'max_height' => 61,
                'max_width' => 61,
                'max_depth' => 46,
                'is_active' => true,
                'amount' => 112.00, // Flat rate amount
                'currency_id' => $internationalCurrency->id,
                'dimensional_weight_divisor' => 50,
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
