<?php

use App\Enums\Order\Discount\DiscountScope;
use App\Enums\Product\ProductType;
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
        'label' => 'Discounts',
        'description' => 'A special discount for selected products',
        'type' => 'percentage',
        'amount' => 10,
        'rate' => 0.1,
        'currency_id' => $currency->id,
        'starts_at' => now(),
        'ends_at' => now()->addDays(30),
        'is_active' => true,
        'usage_limit' => 100,
        'per_user_limit' => 1,
        'min_order_amount' => 50,
        'min_items_quantity' => 2,
        'scope' => DiscountScope::PRODUCT->value,
        'code' => 'SPECIAL10',
        'is_code_required' => true,
        'products' => [
            [
                'product_id' => 1,
                'product_type' => ProductType::PRODUCT->id(),
                'price_id' => 1,
            ],
        ],
    ]
];
