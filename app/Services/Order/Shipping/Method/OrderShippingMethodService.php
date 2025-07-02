<?php
namespace App\Services\Order\Shipping\Method;

use App\Services\BaseService;

class OrderShippingMethodService extends BaseService
{

    public function saveOrderShippingMethod($order, $shippingMethod)
    {
        $order->shipping_method_id = $shippingMethod->id;
        $order->save();

        return $order;
    }
}
