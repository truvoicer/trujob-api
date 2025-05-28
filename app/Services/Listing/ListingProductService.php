<?php

namespace App\Services\Listing;

use App\Contracts\Product\Product;
use App\Models\Discount;
use App\Models\Listing;
use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\ListingRepository;

class ListingProductService implements Product
{
    public function __construct(
        protected ListingRepository $listingRepository,
    ) {
    }

    public function createOrderItem(
        Order $order,
        array $data = []
    ): OrderItem {
        if (empty($data['entity_id'])) {
            throw new \Exception('Entity ID is required to create an order item');
        }
        $listing = $this->listingRepository->findById($data['entity_id'] ?? null);
        if (!$listing) {
            throw new \Exception('Listing does not exist');
        }
        $data['order_id'] = $order->id;
        return $listing->orderItems()->create($data);
    }
    public function updateOrderItem(
        Order $order,
        OrderItem $orderItem,
        array $data = []
    ): OrderItem {
        if (empty($data['entity_id'])) {
            throw new \Exception('Entity ID is required to create an order item');
        }
        $listing = $this->listingRepository->findById($data['entity_id'] ?? null);
        if (!$listing) {
            throw new \Exception('Listing does not exist');
        }
        $existsInOrder = $order->items()->where('id', $orderItem->id)->exists();
        if (!$existsInOrder) {
            throw new \Exception('Order item does not exist in the order');
        }
        if (!empty($data['entity_id'])) {
            $data['productable_id'] = $data['entity_id'];
        }

        if (!empty($data['entity_type'])) {
            $data['productable_type'] = Listing::class;
        }

        if (!$orderItem->update($data)) {
            throw new \Exception('Error updating order item for the listing');
        }
        return $orderItem;
    }

    public function attachDiscountRelations(Discount $discount, array $data = []): Discount
    {
        if (empty($data['product_id'])) {
            throw new \Exception('Product ID is required to create an order item');
        }

        if (empty($data['price_id'])) {
            throw new \Exception('Price ID is required to create an order item');
        }

        $listing = $this->listingRepository->findById($data['product_id'] ?? null);
        if (!$listing) {
            throw new \Exception('Listing does not exist');
        }
        
        $price = $listing->prices()->find($data['price_id']);
        if (!$price) {
            throw new \Exception('Price does not exist for the listing');
        }
        
        $price->discounts()->attach($discount->id);
        return $discount;
    }
}
