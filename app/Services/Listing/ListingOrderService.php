<?php
namespace App\Services\Listing;

use App\Models\Listing;
use App\Models\Order;
use App\Services\Order\OrderService;

class ListingOrderService extends OrderService
{
    public function createListingOrder(Listing $listing, array $data)
    {
        $data['user_id'] = $this->user->id;
        $order = $this->createOrder($data);
        $listing->orders()->attach($order->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return true;
    }

    public function updateListingOrder(Listing $listing, Order $order, array $data)
    {
        $check = $listing->orders()->where('orders.id', $order->id)->first();
        if (!$check) {
            throw new \Exception('Order not found in listing');
        }
        
        $this->updateOrder($order, $data);
        $listing->orders()->updateExistingPivot($order->id, [
            'updated_at' => now(),
        ]);
        return true;
    }
    public function deleteListingOrder(Listing $listing, Order $order)
    {
        $check = $listing->orders()->where('orders.id', $order->id)->first();
        if (!$check) {
            throw new \Exception('Order not found in listing');
        }
        $this->deleteOrder($order);
        $listing->orders()->detach($order->id);
        return true;
    }

}