<?php

namespace App\Factories\Product;

use App\Contracts\Product\Product;
use App\Enums\Product\ProductType;
use App\Services\Listing\ListingProductService;

class ProductFactory
{
    public static function create(ProductType $productType): Product
    {
        return match ($productType) {
            ProductType::LISTING => app()->make(ListingProductService::class),
        };
    }
}
