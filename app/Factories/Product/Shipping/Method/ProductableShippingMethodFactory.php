<?php

namespace App\Factories\Product\Shipping\Method;

use App\Contracts\Product\Shipping\Method\ProductableShippingMethodInterface;
use App\Enums\Product\Shipping\Method\ProductableShippingMethodType;
use App\Services\Product\Shipping\Method\ProductShippingMethodService;

class ProductableShippingMethodFactory
{
    public static function create(ProductableShippingMethodType $productableShippingMethodType): ProductableShippingMethodInterface
    {
        return match ($productableShippingMethodType) {
            ProductableShippingMethodType::PRODUCT => app()->make(ProductShippingMethodService::class),
        };
    }
}
