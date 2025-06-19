<?php

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
    'label' => 'Standard Shipping',
    'description' => 'Delivery within 5-7 business days',
    'is_active' => true,
    'processing_time_days' => 2,
    'order' => 1,
    'rates' => [
        [
            'amount' => 5.00,
            'currency' => $currency->id,
            'is_active' => true,
        ]
    ],
    'restrictions' => [
        [
            'country_id' => $country->id,
            'min_order_amount' => 50,
        ]
    ]
    ]
];
