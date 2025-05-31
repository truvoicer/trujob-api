<?php
namespace App\Services\Product;

use App\Models\Product;
use App\Models\Order;
use App\Services\Order\OrderService;

class ProductOrderService extends OrderService
{
    public function createProductOrder(Product $product, array $data)
    {
        $data['user_id'] = $this->user->id;
        $order = $this->createOrder($data);
        $product->orders()->attach($order->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return true;
    }

    public function updateProductOrder(Product $product, Order $order, array $data)
    {
        $check = $product->orders()->where('orders.id', $order->id)->first();
        if (!$check) {
            throw new \Exception('Order not found in product');
        }
        
        $this->updateOrder($order, $data);
        $product->orders()->updateExistingPivot($order->id, [
            'updated_at' => now(),
        ]);
        return true;
    }
    public function deleteProductOrder(Product $product, Order $order)
    {
        $check = $product->orders()->where('orders.id', $order->id)->first();
        if (!$check) {
            throw new \Exception('Order not found in product');
        }
        $this->deleteOrder($order);
        $product->orders()->detach($order->id);
        return true;
    }

}