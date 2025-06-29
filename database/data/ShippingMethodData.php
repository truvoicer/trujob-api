<?php

use App\Enums\Order\Shipping\ShippingRateType;
use App\Enums\Order\Shipping\ShippingRestrictionAction;
use App\Enums\Order\Shipping\ShippingRestrictionType;
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
$shippingZone = ShippingZone::where('name', 'domestic')->first();
if (!$shippingZone) {
    throw new Exception('Required shipping zone not found.');
}
return [
    [
        'label' => 'Standard Shipping',
        'description' => 'Delivery within 5-7 business days',
        'is_active' => true,
        'processing_time_days' => 2,
        'rates' => [
            [
                'shipping_zone_id' => $shippingZone->id,
                'type' => ShippingRateType::FLAT_RATE->value,
                'weight_limit' => false,
                'height_limit' => false,
                'width_limit' => false,
                'length_limit' => false,
                'weight_unit' => null,
                'height_unit' => null,
                'width_unit' => null,
                'length_unit' => null,
                'min_weight' => null,
                'max_weight' => null,
                'min_height' => null,
                'max_height' => null,
                'min_width' => null,
                'max_width' => null,
                'min_length' => null,
                'max_length' => null,
                'amount' => 5.00, // Flat rate amount
                'currency_id' => $currency->id,
                'is_active' => true
            ]
        ],
        'restrictions' => [
            [
                'type' => ShippingRestrictionType::COUNTRY->value,
                'restriction_id' =>  $country->id,
                'action' => ShippingRestrictionAction::ALLOW->value,
            ],
        ]
    ]
];
