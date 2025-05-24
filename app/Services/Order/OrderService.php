<?php

namespace App\Services\Order;

use App\Models\Order;
use App\Services\BaseService;
use App\Services\Listing\ListingsAdminService;

class OrderService extends BaseService
{
    public function createOrder(array $data) {
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
    public function updateOrder(Order $transaction, array $data) {
        if (!$transaction->update($data)) {
            throw new \Exception('Error updating listing transaction');
        }
        return $transaction;
    }

    public function deleteOrder(Order $transaction) {
        if (!$transaction->delete()) {
            throw new \Exception('Error deleting listing transaction');
        }
        return true;
    }
    public function createBulkOrderItems(Order $order, array $items) {
        $createdItems = [];
        foreach ($items as $itemData) {
            $createdItems[] = $this->createOrderItem($order, $itemData);
        }
        return $createdItems;
    }
    public function createOrderItem(Order $order, array $data) {
        switch ($data['entity_type']) {
            case 'listing':
                $listingService = app()->make(ListingsAdminService::class);
                $listing = $listingService->getListingById($data['entity_id']);
                return $listingService->createOrderItem($order, $listing, $data);
            default:
                throw new \Exception('Invalid itemable type');
        }
    }

}
