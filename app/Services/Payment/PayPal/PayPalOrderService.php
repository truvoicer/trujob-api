<?php

namespace App\Services\Payment\PayPal;

use App\Enums\Order\OrderItemable;
use App\Enums\Price\PriceType;
use App\Exceptions\Product\ProductHealthException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
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
        if (!$product instanceof Product) {
            throw new \Exception('Product not found for order item');
        }

        $healthCheckData = $product->healthCheck();
        if ($healthCheckData['unhealthy']['count'] > 0) {
            throw new ProductHealthException(
                $product,
                $healthCheckData
            );
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
        $item->setSku($product->sku);
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
        $order->init();

        foreach ($order->items as $item) {

            $item->setPriceType(PriceType::ONE_TIME);
            $item->init();
            $orderItem = $this->createOrderItem($item);
            if (!$orderItem) {
                throw new \Exception('Error creating PayPal order item');
            }
            $this->payPalService->addItem($orderItem);
        }
        // 'total_price' => $this->calculateTotalPrice(),
        //     'total_quantity' => $this->calculateTotalQuantity(),
        //     'total_tax' => $this->calculateTotalTax(),
        //     'total_discount' => $this->calculateTotalDiscount(),
        //     'final_total' => $this->calculateFinalTotal(),
        //     'total_items' => $this->calculateTotalItems(),
        //     'average_price_per_item' => $this->calculateAveragePricePerItem(),
        //     'total_price_after_discounts' => $this->calculateTotalPriceAfterDiscounts(),
        //     'total_price_after_tax' => $this->calculateTotalPriceAfterTax(),
        //     'total_price_after_tax_and_discounts' => $this->calculateTotalPriceAfterTaxAndDiscounts(),
        $this->payPalService->setCurrencyCode($order->currency_code);
        $this->payPalService->setValue($order->calculateFinalTotal());
        $createOrder = $this->payPalService->createOrder();
        dd($createOrder);
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
