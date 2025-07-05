<?php

namespace App\Services\Order\Item;

use App\Enums\Order\OrderItemable;
use App\Factories\Order\OrderItemFactory;
use App\Helpers\ProductHelpers;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\BaseService;
use App\Services\Product\ProductAdminService;

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
        return OrderItemFactory::create(
            ProductHelpers::validateProductableByArray('entity_type', $data)
        )
            ->createOrderItem(
                $order,
                $data
            );
    }

    public function updateOrderItem(Order $order, OrderItem $orderItem, array $data)
    {
        return OrderItemFactory::create(
            $orderItem->order_itemable_type
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
