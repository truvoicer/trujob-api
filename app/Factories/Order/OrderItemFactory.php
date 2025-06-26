<?php

namespace App\Factories\Order;

use App\Contracts\Product\Product;
use App\Enums\Order\OrderItemable;
use App\Services\Product\ProductProductService;

class OrderItemFactory
{
    public static function create(OrderItemable $orderItemableType): Product
    {
        return match ($orderItemableType) {
            OrderItemable::PRODUCT => app()->make(ProductProductService::class),
        };
    }
}
