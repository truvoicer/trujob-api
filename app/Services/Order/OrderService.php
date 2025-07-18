<?php

namespace App\Services\Order;

use App\Enums\Price\PriceType;
use App\Models\Order;
use App\Services\BaseService;
use App\Services\Order\Item\OrderItemService;

class OrderService extends BaseService
{
    public function __construct(
        private OrderItemService $orderItemService,
    ) {
        parent::__construct();
    }

    public function createOrder(array $data)
    {
        $orderItems = [];
        if (!empty($data['items'])) {
            $orderItems = $data['items'];
            unset($data['items']);
        }

        $this->orderItemService->validateBulkOrderItems($orderItems);
        $data['currency_id'] = $this->user?->userSetting?->currency?->id ?? null;
        if (empty($data['currency_id'])) {
            throw new \Exception('Currency ID is required to create an order');
        }
        $data['country_id'] = $this->user?->userSetting?->country?->id ?? null;
        if (empty($data['country_id'])) {
            throw new \Exception('Country ID is required to create an order');
        }
        $order = $this->user->orders()->create($data);

        if (!$order->exists()) {
            throw new \Exception('Error creating product order');
        }

        $createdItems = [];
        if (count($orderItems)) {
            $createdItems = $this->orderItemService->createBulkOrderItems($order, $orderItems);
            if (empty($createdItems)) {
                throw new \Exception('Error creating order items');
            }
        }
        return $order;
    }
    public function updateOrder(Order $order, array $data)
    {
        if (!$order->update($data)) {
            throw new \Exception('Error updating product order');
        }
        return $order;
    }

    public function deleteOrder(Order $order)
    {
        if (!$order->delete()) {
            throw new \Exception('Error deleting product order');
        }
        return true;
    }

    public function syncDiscounts(Order $order, array $discountIds)
    {
        $order->discounts()->sync($discountIds);
        return true;
    }

    public function getOneTimeOrder(Order $order)
    {
        return $order->load(['items']);
    }
    public function getSubscriptionOrder(Order $order)
    {
        return $order->load([
            'items' => function ($query) {
                $query->with([
                    'orderItemable' => function ($query) {
                        $query->with([
                            'prices' => function ($query) {
                                $query->where(
                                    'price_type',
                                    PriceType::SUBSCRIPTION->value
                                )
                                    ->where('is_active', true)
                                    ->where('currency_id', $this->user?->userSetting?->currency?->id ?? null)
                                    ->where('country_id', $this->user?->userSetting?->country?->id ?? null);
                            },
                        ]);
                    },
                ]);
            },
        ]);
    }

    public function getOrderByPriceType(Order $order)
    {
        switch ($order->price_type) {
            case PriceType::ONE_TIME:
                return $this->getOneTimeOrder($order);
            case PriceType::SUBSCRIPTION:
                return $this->getSubscriptionOrder($order);
            default:
                throw new \Exception('Invalid price type');
        }
    }
}
