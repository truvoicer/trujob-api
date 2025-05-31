<?php

namespace App\Contracts\Product;

use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

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
    public function findMany(
        string $sort = 'name',
        string $order = 'asc',
        int $perPage = 10,
        int $page = 1,
        ?string $search = null
    ): Collection|LengthAwarePaginator;
}
