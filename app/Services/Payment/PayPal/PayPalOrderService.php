<?php

namespace App\Services\Payment\PayPal;

use App\Enums\Order\OrderItemable;
use App\Enums\Price\PriceType;
use App\Enums\Transaction\TransactionStatus;
use App\Exceptions\Product\ProductHealthException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\BaseService;
use App\Services\Order\Transaction\OrderTransactionService;
use App\Services\Payment\PayPal\PayPalService;
use PaypalServerSdkLib\Models\Item;
use PaypalServerSdkLib\Models\Money;

class PayPalOrderService extends BaseService
{


    public function __construct(
        private PayPalService $payPalService,
        private OrderTransactionService $orderTransactionService,
    ) {
        // Initialize any PayPal SDK or configuration here
        parent::__construct();
    }

    private function initializePayPalService(): void
    {

        $sitePaypal = $this->site->paymentGateways()
            ->where('name', 'paypal')
            ->first()?->pivot ?? null;

        if (empty($sitePaypal)) {
            throw new \Exception('PayPal payment gateway is not set in site payment gateways');
        }
        $settings = $sitePaypal?->settings ?? null;
        if (empty($settings)) {
            throw new \Exception('PayPal settings are not set in site payment gateway settings');
        }

        $clientId = $settings['client_id'] ?? null;
        if (empty($clientId)) {
            throw new \Exception('PayPal client ID is not set in site payment gateway settings');
        }
        $clientSecret = $settings['client_secret'] ?? null;
        if (empty($clientSecret)) {
            throw new \Exception('PayPal client secret is not set in site payment gateway settings');
        }

        $webhookId = $settings['webhook_id'] ?? null;
        if (empty($webhookId)) {
            throw new \Exception('PayPal webhook ID is not set in site payment gateway settings');
        }

        $environment = $sitePaypal->environment;
        if (empty($environment)) {
            throw new \Exception('PayPal environment is not set in site payment gateway');
        }
        $this->payPalService->setEnvironment($environment);
        $this->payPalService->setClientId($clientId);
        $this->payPalService->setClientSecret($clientSecret);
        $this->payPalService->setWebhookId($webhookId);
        $this->payPalService->init();
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
        $paypalOrderItem = new Item(
            $product->title,
            $itemAmount,
            $item->quantity
        );
        $paypalOrderItem->setDescription($product->description);
        $paypalOrderItem->setTax($itemTax);
        $paypalOrderItem->setQuantity($item->quantity);
        $paypalOrderItem->setSku($product->sku);
        // $paypalOrderItem->setCategory($product->productCategories->first()?->name ?? 'General');
        return $paypalOrderItem;
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
    public function createOrder(Order $order, Transaction $transaction)
    {
        $this->orderTransactionService->setUser($this->user);
        $this->orderTransactionService->setSite($this->site);

        $this->initializePayPalService();
        $order->setPriceType($order->price_type);
        $order->init();

        foreach ($order->items as $item) {

            $item->setPriceType($order->price_type);
            $item->init();
            $orderItem = $this->createOrderItem($item);
            if (!$orderItem) {
                throw new \Exception('Error creating PayPal order item');
            }
            $this->payPalService->addItem($orderItem);
        }

        $finalTotal = $order->calculateFinalTotal();
        $currencyCode = $order->currency?->code;
        $this->payPalService->setCurrencyCode($currencyCode);
        $this->payPalService->setValue($finalTotal);
        $this->payPalService->setItemTotal($order->calculateTotalPrice());
        $this->payPalService->setTaxTotal($order->calculateTotalTax());
        $this->payPalService->setDiscount($order->calculateTotalDiscount());
        $responseHandler = $this->payPalService->createOrder();


        if (!$responseHandler->isSuccess()) {
            $errorMessage = $responseHandler->getErrorMessage();
            $errorDetails = $responseHandler->getErrorDetails();
            $this->orderTransactionService->updateTransaction(
                $order,
                $transaction,
                [
                    'currency_code' => $currencyCode,
                    'status' => TransactionStatus::FAILED,
                    'amount' => $finalTotal,
                    'order_data' => $responseHandler->getResult(),
                ]
            );
            // Log the error or throw a more specific exception
            throw new \Exception(
                'Error creating PayPal order: ' . $errorMessage . ' Details: ' . json_encode($errorDetails)
            );
        }

        $this->orderTransactionService->updateTransaction(
            $order,
            $transaction,
            [
                'currency_code' => $currencyCode,
                'status' => TransactionStatus::PROCESSING,
                'amount' => $finalTotal,
                'order_data' => $responseHandler->getResult(),
            ]
        );
        // Order created successfully, return relevant information
        return $responseHandler->getResult();
    }

    public function getOrder(string $orderId)
    {
        $this->initializePayPalService();
        $response = $this->payPalService->getOrder($orderId);
        if (!$response->isSuccess()) {
            $errorMessage = $response->getErrorMessage();
            $errorDetails = $response->getErrorDetails();
            // Log the error or throw a more specific exception
            throw new \Exception(
                'Error creating PayPal order: ' . $errorMessage . ' Details: ' . json_encode($errorDetails)
            );
        }

        // Order created successfully, return relevant information
        return $response->getResult();
    }

    public function captureOrder(Order $order, Transaction $transaction, string $orderId)
    {
        $this->initializePayPalService();
        $response = $this->payPalService->captureOrder($orderId);
        if (!$response->isSuccess()) {
            $errorMessage = $response->getErrorMessage();
            $errorDetails = $response->getErrorDetails();
            // Log the error or throw a more specific exception

            $this->orderTransactionService->updateTransaction(
                $order,
                $transaction,
                [
                    'status' => TransactionStatus::FAILED,
                    'transaction_data' => $response->getResult(),
                ]
            );
            throw new \Exception(
                'Error capturing PayPal order: ' . $errorMessage . ' Details: ' . json_encode($errorDetails)
            );
        }
        $this->orderTransactionService->updateTransaction(
            $order,
            $transaction,
            [
                'status' => TransactionStatus::COMPLETED,
                'transaction_data' => $response->getResult(),
            ]
        );
        // Order captured successfully, return relevant information
        return $response->getResult();
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
