<?php

namespace App\Services\Product;

use App\Contracts\Product\Product as ProductContract;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductProductService implements ProductContract
{
    public function __construct(
        protected ProductRepository $productRepository,
    ) {
    }

    public function findMany(
        string $sort = 'name',
        string $order = 'asc',
        int $perPage = 10,
        int $page = 1,
        ?string $search = null
    ): Collection|LengthAwarePaginator {
        $this->productRepository->setPagination(true);
        $this->productRepository->setOrderByColumn($sort);
        $this->productRepository->setOrderByDir($order);
        $this->productRepository->setPerPage($perPage);
        $this->productRepository->setPage($page);
        if ($search) {
            $this->productRepository->addWhere(
                'title',
                "%$search%",
                'like',
            );
        }
        return $this->productRepository->findMany();
    }

    public function createOrderItem(
        Order $order,
        array $data = []
    ): OrderItem {
        if (empty($data['entity_id'])) {
            throw new \Exception('Entity ID is required to create an order item');
        }
        $product = $this->productRepository->findById($data['entity_id'] ?? null);
        if (!$product) {
            throw new \Exception('Product does not exist');
        }
        $data['order_id'] = $order->id;
        return $product->orderItems()->create($data);
    }

    public function updateOrderItem(
        Order $order,
        OrderItem $orderItem,
        array $data = []
    ): OrderItem {
        $product = $this->productRepository->findById($orderItem->order_itemable_id);
        if (!$product) {
            throw new \Exception('Product does not exist');
        }
        $existsInOrder = $order->items()->where('id', $orderItem->id)->exists();
        if (!$existsInOrder) {
            throw new \Exception('Order item does not exist in the order');
        }

        if (!$orderItem->update($data)) {
            throw new \Exception('Error updating order item for the product');
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

        $product = $this->productRepository->findById($data['product_id'] ?? null);
        if (!$product) {
            throw new \Exception('Product does not exist');
        }

        $price = $product->prices()->find($data['price_id']);
        if (!$price) {
            throw new \Exception('Price does not exist for the product');
        }

        $price->discounts()->attach($discount->id);
        return $discount;
    }
}
