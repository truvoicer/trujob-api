<?php

use App\Enums\Order\Shipping\ShippingZoneAbleType;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Region;

$country = Country::where('iso2', 'GB')->first();
if (!$country) {
    throw new Exception('Required country not found.');
}
$currency = $country->currency()->where('code', 'GBP')->first();
if (!$currency) {
    throw new Exception('Required currency not found.');
}
$region = Region::where('name', 'Asia')->first();
if (!$region) {
    throw new Exception('Required region not found.');
}
return [

    [
        'name' => 'domestic',
        'label' => 'Domestic',
        'description' => 'Shipping zone for domestic orders',
        'is_active' => true,
        'all' => false,
        'shipping_zoneables' => [
            [
                'shipping_zoneable_type' => ShippingZoneAbleType::COUNTRY->value,
                'shipping_zoneable_id' => $country->id,
            ]
        ],
    ],
    [
        'name' => 'international',
        'label' => 'International',
        'description' => 'Shipping zone for international orders',
        'is_active' => true,
        'all' => false,
        'shipping_zoneables' => [
            [
                'shipping_zoneable_type' => ShippingZoneAbleType::COUNTRY->value,
                'shipping_zoneable_id' => $country->id,
            ]
        ],
    ],
    [
        'name' => 'Asia Shipping',
        'label' => 'Asia Shipping',
        'description' => 'Shipping zone for Asia',
        'is_active' => true,
        'all' => false,
        'shipping_zoneables' => [
            [
                'shipping_zoneable_type' => ShippingZoneAbleType::REGION->value,
                'shipping_zoneable_id' => $region->id,
            ]
        ],
    ],
];
