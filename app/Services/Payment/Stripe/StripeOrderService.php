<?php

namespace App\Services\Payment\Stripe;

use App\Enums\Order\OrderItemable;
use App\Enums\Payment\PaymentGateway;
use App\Enums\Price\PriceType;
use App\Enums\Transaction\TransactionStatus;
use App\Exceptions\PaymentGateway\StripeRequestException;
use App\Exceptions\Product\ProductHealthException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\BaseService;
use App\Services\Order\Transaction\OrderTransactionService;
use App\Services\Payment\PayPal\Middleware\Order\PayPalOrderService as PaypalOrderServiceSdk;
use Stripe\Exception\ApiErrorException;

class StripeOrderService extends BaseService
{


    public function __construct(
        private PaypalOrderServiceSdk $payPalService,
        private OrderTransactionService $orderTransactionService,
    ) {
        // Initialize any PayPal SDK or configuration here
        parent::__construct();
    }

    private function initializeStripeService(): void
    {

        $site = $this->site ?? null;

        if (!$site) {
            throw new \Exception('Site not found for the user');
        }

        $siteCurrency = $site?->settings?->currency ?? null;
        $siteLocale = $site?->settings?->locale ?? null;

        if (!$siteCurrency) {
            throw new \Exception('Site currency not found');
        }

        if (!$siteCurrency->code) {
            throw new \Exception('Site currency code not found');
        }

        if (!$siteLocale) {
            throw new \Exception('Site locale not found');
        }

        $stripePaymentGateway = $site->activePaymentGatewayByName(PaymentGateway::STRIPE)
            ->first()?->pivot ?? null;
        if (!$stripePaymentGateway) {
            throw new \Exception('Stripe payment gateway not found');
        }

        $key = $stripePaymentGateway->settings['publishable_key'] ?? null;
        $secret = $stripePaymentGateway->settings['secret_key'] ?? null;
        $success_url = $stripePaymentGateway->settings['success_url'] ?? null;
        $cancel_url = $stripePaymentGateway->settings['cancel_url'] ?? null;
        $webhook_secret = $stripePaymentGateway->settings['webhook_secret'] ?? null;

        if (!$key) {
            throw new \Exception('Stripe publishable key not found');
        }

        if (!$secret) {
            throw new \Exception('Stripe secret key not found');
        }

        if (!$success_url) {
            throw new \Exception('Stripe success URL not found');
        }

        if (!$cancel_url) {
            throw new \Exception('Stripe cancel URL not found');
        }

        // if (!$webhook_secret) {
        //     throw new \Exception('Stripe webhook secret not found');
        // }

        // Set the currency and locale for Cashier
        config(['cashier.currency' => $siteCurrency->code]);
        config(['cashier.currency_locale' => $siteLocale]);
        config(['cashier.key' => $key]);
        config(['cashier.secret' => $secret]);
    }

    public function createProductOrderItem(OrderItem $item): array
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
        return [
            'price_data' => [
                'currency' => $price->currency->code,
                'product_data' => [
                    'name' => $product->title,
                    'description' => $product->description,
                ],
                'unit_amount' => $item->calculateTotalPrice() * 100, // Amount in cents
            ],
            'quantity' => $item->quantity,
        ];
    }

    public function createOrderItem(OrderItem $item): array|null
    {
        switch ($item->order_itemable_type) {
            case OrderItemable::PRODUCT:
                return $this->createProductOrderItem($item);
                break;
        }
        return null;
    }
    public function createCheckoutSession(Order $order, Transaction $transaction)
    {
        $this->orderTransactionService->setUser($this->user);
        $this->orderTransactionService->setSite($this->site);

        $this->initializeStripeService();
        $order->setPriceType($order->price_type);
        $order->init();
        $lineItems = [];
        foreach ($order->items as $item) {

            $item->setPriceType($order->price_type);
            $item->init();
            $orderItem = $this->createOrderItem($item);
            if (!$orderItem) {
                throw new \Exception('Error creating PayPal order item');
            }
            $lineItems[] = $orderItem;
        }

        $stripePaymentGateway = $this->site->activePaymentGatewayByName(PaymentGateway::STRIPE)
            ->first()?->pivot ?? null;
        if (!$stripePaymentGateway) {
            throw new \Exception('Stripe payment gateway not found');
        }

        $return_url = $stripePaymentGateway->settings['return_url'] ?? null;
        $finalTotal = $order->calculateFinalTotal();
        $currencyCode = $order->currency?->code;
        try {
            $createData = [
                    'payment_method_types' => ['card'],
                    'line_items' => $lineItems,
                    'ui_mode' => 'custom', // <-- ADD THIS LINE
                    'mode' => 'payment',
            ];
            if ($return_url) {
                $createData['return_url'] = $return_url;
            } else {
                // $createData['redirect_on_completion'] = 'never';
            }
            $responseHandler = Cashier::stripe()
                ->checkout
                ->sessions
                ->create(
                    $createData
                );

            $this->orderTransactionService->updateTransaction(
                $order,
                $transaction,
                [
                    'currency_code' => $currencyCode,
                    'status' => TransactionStatus::PROCESSING,
                    'amount' => $finalTotal,
                    'order_data' => $responseHandler->toArray(),
                ]
            );
            return $responseHandler;
        } catch (ApiErrorException $e) {
            $this->orderTransactionService->updateTransaction(
                $order,
                $transaction,
                [
                    'currency_code' => $currencyCode,
                    'status' => TransactionStatus::FAILED,
                    'amount' => $finalTotal,
                    'order_data' => $e->getJsonBody(),
                ]
            );
            // Log the error or throw a more specific exception
            throw new StripeRequestException(
                [
                    'error_code' => $e->getHttpStatus(),
                    'stripe_code' => $e->getStripeCode(),
                ],
                'Stripe Checkout Session Creation Error: ' . $e->getMessage()
            );
        } catch (\Exception $e) {
            // Log the error or throw a more specific exception
            throw new StripeRequestException(
                [],
                'Stripe Checkout Session Creation Error: ' . $e->getMessage()
            );
        }
        // Order created successfully, return relevant information
        return false;
    }
}
