<?php

namespace App\Services\Order\Item;

use App\Enums\Product\ProductType;
use App\Factories\Product\ProductFactory;
use App\Helpers\ProductHelpers;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\BaseService;
use App\Services\Listing\ListingsAdminService;

class OrderItemService extends BaseService
{

    public function createBulkOrderItems(Order $order, array $items)
    {
        $createdItems = [];
        foreach ($items as $itemData) {
            $createdItems[] = $this->createOrderItem($order, $itemData);
        }
        return $createdItems;
    }

    public function createOrderItem(Order $order, array $data)
    {
        return ProductFactory::create(
            ProductHelpers::validateProductableByArray('entity_type', $data)
        )
            ->createOrderItem(
                $order,
                $data
            );
    }

    public function updateOrderItem(Order $order, OrderItem $orderItem, array $data)
    {
        return ProductFactory::create(
            ProductHelpers::validateProductableByArray('entity_type', $data)
        )
            ->updateOrderItem(
                $order,
                $orderItem,
                $data
            );
    }

    public function deleteOrderItem(OrderItem $orderItem)
    {
        if (!$orderItem->delete()) {
            throw new \Exception('Error deleting order item');
        }
        return true;
    }
}
