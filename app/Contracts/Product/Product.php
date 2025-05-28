<?php

namespace App\Contracts\Product;

use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;

interface Product
{
    public function createOrderItem(
        Order $order,
        array $data = []
    ): OrderItem;
    public function updateOrderItem(
        Order $order,
        OrderItem $orderItem,
        array $data = []
    ): OrderItem;
    public function attachDiscountRelations(
        Discount $discount,
        array $data = []
    ): Discount;
}
