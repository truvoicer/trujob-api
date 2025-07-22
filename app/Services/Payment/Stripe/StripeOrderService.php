<?php

namespace App\Services\Payment\Stripe;

use App\Enums\Order\OrderItemable;
use App\Enums\Payment\PaymentGateway;
use App\Enums\Transaction\TransactionPaymentStatus;
use App\Enums\Transaction\TransactionStatus;
use App\Exceptions\PaymentGateway\StripeRequestException;
use App\Exceptions\Product\ProductHealthException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\Payment\Stripe\Middleware\Checkout\StripeCheckoutSessionBuilder;
use Stripe\Exception\ApiErrorException;

class StripeOrderService extends StripeBaseOrderService
{

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
            $responseHandler = $this->stripeCheckoutService->createOneTimePaymentSession(
                StripeCheckoutSessionBuilder::make()
                    ->setLineItems($lineItems)
                    ->setMode('payment')
                    ->setUiMode('custom')
                    ->setPaymentMethodTypes(['card'])
                    ->setReturnUrl(
                        ($return_url) ? $return_url : null
                    )
            );
            $this->orderTransactionService->updateTransaction(
                $order,
                $transaction,
                [
                    'currency_code' => $currencyCode,
                    'status' => TransactionStatus::PROCESSING,
                    'payment_status' => TransactionPaymentStatus::UNPAID,
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
                    'payment_status' => TransactionPaymentStatus::UNPAID,
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



    public function handleOneTimePaymentApproval(Order $order, Transaction $transaction, array $data)
    {
        $this->orderTransactionService->setUser($this->user);
        $this->orderTransactionService->setSite($this->site);

        $this->initializeStripeService();

        if (empty($data['id'])) {
            $this->orderTransactionService->updateTransaction(
                $order,
                $transaction,
                [
                    'status' => TransactionStatus::FAILED,
                    'payment_status' => TransactionPaymentStatus::UNPAID,
                    'transaction_data' => $data,
                ]
            );
            throw new \Exception(
                'Subscription ID is missing in the approval data: ' . json_encode($data)
            );
        }

        try {

            return $this->handleApproveResponse(
                $order,
                $transaction,
                $this->stripeCheckoutService->retrieveSession(
                    $data['id']
                )
            );
        } catch (ApiErrorException $e) {
            $this->orderTransactionService->updateTransaction(
                $order,
                $transaction,
                [
                    'status' => TransactionStatus::FAILED,
                    'payment_status' => TransactionPaymentStatus::UNPAID,
                    'transaction_data' => $e->getJsonBody(),
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
            $this->orderTransactionService->updateTransaction(
                $order,
                $transaction,
                [
                    'status' => TransactionStatus::FAILED,
                    'payment_status' => TransactionPaymentStatus::UNPAID,
                    'transaction_data' => [
                        'error' => $e->getMessage(),
                        'data' => $data,
                    ]
                ]
            );
            throw new StripeRequestException(
                [],
                'Stripe Checkout Session Creation Error: ' . $e->getMessage()
            );
        }
        // Order created successfully, return relevant information
        return false;
    }

    public function handleOneTimePaymentCancel(Order $order, Transaction $transaction, array $data): bool
    {
        $this->orderTransactionService->setUser($this->user);
        $this->orderTransactionService->setSite($this->site);

        $this->orderTransactionService->updateTransaction(
            $order,
            $transaction,
            [
                'status' => TransactionStatus::CANCELLED,
                'payment_status' => TransactionPaymentStatus::UNPAID,
                'transaction_data' => $data,
            ]
        );
        return true;
    }
}
