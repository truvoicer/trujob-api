<?php

namespace App\Services\Order\Item;

use App\Models\Order;
use App\Models\OrderItem;
use App\Services\BaseService;
use App\Services\Listing\ListingsAdminService;

class OrderItemService extends BaseService
{

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

    public function updateOrderItem(Order $order, OrderItem $orderItem, array $data) {
        if (empty($data['entity_type'])) {
            if (!$orderItem->update($data)) {
                throw new \Exception('Error updating order item');
            }
            return true;
        }
        switch ($data['entity_type']) {
            case 'listing':
                $listingService = app()->make(ListingsAdminService::class);
                $listing = $listingService->getListingById($data['entity_id']);
                return $listingService->updateOrderItem($order, $orderItem, $listing, $data);
            default:
                throw new \Exception('Invalid itemable type');
        }
    }

    public function deleteOrderItem(OrderItem $orderItem) {
        if (!$orderItem->delete()) {
            throw new \Exception('Error deleting order item');
        }
        return true;
    }

    
}
