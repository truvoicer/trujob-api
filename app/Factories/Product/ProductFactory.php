<?php

namespace App\Factories\Product;

use App\Contracts\Product\Product;
use App\Enums\Product\ProductType;
use App\Services\Product\ProductProductService;

class ProductFactory
{
    public static function create(ProductType $productType): Product
    {
        return match ($productType) {
            ProductType::PRODUCT => app()->make(ProductProductService::class),
        };
    }
}
