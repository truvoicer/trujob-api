<?php

use App\Enums\Order\Discount\DiscountableType;
use App\Enums\Order\Discount\DiscountAmountType;
use App\Enums\Order\Discount\DiscountScope;
use App\Enums\Order\Discount\DiscountType;
use App\Enums\Price\PriceType;
use App\Enums\Product\ProductType;
use App\Models\Country;
use App\Models\Product;

$country = Country::where('iso2', 'GB')->first();
if (!$country) {
    throw new Exception('Required country not found.');
}
$currency = $country->currency()->where('code', 'GBP')->first();
if (!$currency) {
    throw new Exception('Required currency not found.');
}
$firstProduct = Product::first();
if (!$firstProduct) {
    throw new Exception('Required product not found.');
}
$firstProductOneTimePrice = $firstProduct->prices()->whereRelation('priceType', 'name', PriceType::ONE_TIME->value)->first();
if (!$firstProductOneTimePrice) {
    throw new Exception('Required product one-time price not found.');
}
$secondProduct = Product::where('id', 2)->first();
if (!$secondProduct) {
    throw new Exception('Required second product not found.');
}
$secondProductOneTimePrice = $secondProduct->prices()->whereRelation('priceType', 'name', PriceType::ONE_TIME->value)->first();
if (!$secondProductOneTimePrice) {
    throw new Exception('Required second product one-time price not found.');
}

return [
    [
        'label' => 'Buy 2 Get 1 Free',
        'description' => 'Buy 2 products and get 1 free',
        'type' => DiscountType::BUY_X_GET_Y->value,
        'amount_type' => DiscountAmountType::PERCENTAGE->value,
        'amount' => 10,
        'rate' =>   1,
        'currency_id' => $currency->id,
        'starts_at' => now(),
        'ends_at' => now()->addDays(30),
        'is_active' => true,
        'is_default' => true,
        'usage_limit' => 100,
        'per_user_limit' => 1,
        'min_order_amount' => 0,
        'min_items_quantity' => 1,
        'scope' => DiscountScope::ORDER->value,
        'code' => 'SALE10',
        'is_code_required' => true,
        'discountables' => [
            [
                'discountable_id' => $secondProductOneTimePrice->id,
                'discountable_type' => DiscountableType::PRICE->value,
            ],
        ],
    ],
    [
        'label' => '10% off all Sale',
        'description' => 'A special 10% discount for all',
        'amount_type' => DiscountAmountType::PERCENTAGE->value,
        'amount' => 10,
        'rate' =>   10,
        'currency_id' => $currency->id,
        'starts_at' => now(),
        'ends_at' => now()->addDays(30),
        'is_active' => true,
        'is_default' => true,
        'usage_limit' => 100,
        'per_user_limit' => 1,
        'min_order_amount' => 0,
        'min_items_quantity' => 1,
        'scope' => DiscountScope::ORDER->value,
        'code' => 'SALE10',
        'is_code_required' => true,
    ],
    [
        'label' => '10% off First Product',
        'description' => 'A special 10% discount for first product',
        'amount_type' => DiscountAmountType::PERCENTAGE->value,
        'amount' => 10,
        'rate' =>   1,
        'currency_id' => $currency->id,
        'starts_at' => now(),
        'ends_at' => now()->addDays(30),
        'is_active' => true,
        'usage_limit' => 100,
        'per_user_limit' => 1,
        'min_order_amount' => 50,
        'min_items_quantity' => 2,
        'scope' => DiscountScope::PRODUCT->value,
        'code' => 'SPECIALFIRST10',
        'is_code_required' => true,
        'discountables' => [
            [
                'discountable_id' => $firstProductOneTimePrice->id,
                'discountable_type' => DiscountableType::PRICE->value,
            ],
        ],
    ],
    [
        'label' => 'Free shipping',
        'description' => 'Free shipping for orders over £50',
        'amount_type' => DiscountAmountType::PERCENTAGE->value,
        'type' => DiscountType::FREE_SHIPPING->value,
        'amount' => 10,
        'rate' =>   1,
        'currency_id' => $currency->id,
        'starts_at' => now(),
        'ends_at' => now()->addDays(30),
        'is_active' => true,
        'usage_limit' => 100,
        'per_user_limit' => 1,
        'min_order_amount' => 50,
        'min_items_quantity' => 2,
        'scope' => DiscountScope::ORDER->value,
        'code' => 'FREESHIP50',
        'is_code_required' => true,
    ],
];
