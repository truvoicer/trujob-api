<?php

namespace App\Services\Order;

use App\Factories\Product\ProductFactory;
use App\Helpers\ProductHelpers;
use App\Models\Order;
use App\Services\BaseService;
use App\Services\Listing\ListingsAdminService;

class OrderService extends BaseService
{
    public function createOrder(array $data)
    {
        $orderItems = [];
        if (!empty($data['items'])) {
            $orderItems = $data['items'];
            unset($data['items']);
        }
        $order = $this->user->orders()->create($data);

        if (!$order->exists()) {
            throw new \Exception('Error creating listing order');
        }
        $createdItems = [];
        if (count($orderItems)) {
            $createdItems = $this->createBulkOrderItems($order, $orderItems);
            if (empty($createdItems)) {
                throw new \Exception('Error creating order items');
            }
        }
        return $order;
    }
    public function updateOrder(Order $transaction, array $data)
    {
        if (!$transaction->update($data)) {
            throw new \Exception('Error updating listing transaction');
        }
        return $transaction;
    }

    public function deleteOrder(Order $transaction)
    {
        if (!$transaction->delete()) {
            throw new \Exception('Error deleting listing transaction');
        }
        return true;
    }
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

    public function syncDiscounts(Order $order, array $discountIds)
    {
        $order->discounts()->sync($discountIds);
        return true;
    }
}
