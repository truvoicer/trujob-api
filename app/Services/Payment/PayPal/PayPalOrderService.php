<?php

namespace App\Services\Payment\PayPal;

use App\Enums\Order\OrderItemable;
use App\Enums\Price\PriceType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\BaseService;
use App\Services\Payment\PayPal\PayPalService;
use PaypalServerSdkLib\Models\Item;
use PaypalServerSdkLib\Models\Money;

class PayPalOrderService extends BaseService
{


    public function __construct(
        private PayPalService $payPalService
    ) {
        // Initialize any PayPal SDK or configuration here
        parent::__construct();
    }

    public function createProductOrderItem(OrderItem $item): Item
    {
        $product = $item->orderItemable;
        if (!$product) {
            throw new \Exception('Product not found for order item');
        }
        $price = $item->getOrderItemPrice();
        $itemAmount = new Money(
            $price->currency->code,
            $item->calculateTotalPrice()
        );
        $itemTax = new Money(
            $price->currency->code,
            $item->calculateTaxWithoutPrice($item->calculateTotalPrice()),
        );
        $item = new Item($item->name, $itemAmount, $item->quantity);
        $item->setDescription($product->description);
        $item->setTax($itemTax);
        $item->setName($product->title);
        $item->setQuantity($item->quantity);
        $item->setSku($product->generateSku());
        $item->setCategory($product->productCategories->first()->name ?? 'General');
        return $item;
    }

    public function createOrderItem(OrderItem $item): Item|null
    {

        switch ($item->order_itemable_type) {
            case OrderItemable::PRODUCT:
                return $this->createProductOrderItem($item);
                break;
        }
        return null;
    }
    public function createOrder(Order $order)
    {

        $order->setPriceType(PriceType::ONE_TIME);
        $order->init($this->user);

        foreach ($order->items as $item) {

            $item->setPriceType(PriceType::ONE_TIME);
            $item->init($this->user);
            $orderItem = $this->createOrderItem($item);
            if (!$orderItem) {
                throw new \Exception('Error creating PayPal order item');
            }
            $this->payPalService->addItem($orderItem);
        }

        // $createOrder = $this->payPalService->createOrder($data);
        // dd($createOrder);
        // if (!$createOrder) {
        //     throw new \Exception('Error creating PayPal order');
        // }
        // return $createOrder;
    }

    public function updateOrder(string $orderId, array $data)
    {
        // Logic to update a PayPal order
    }

    public function cancelOrder(string $orderId)
    {
        // Logic to cancel a PayPal order
    }
}
