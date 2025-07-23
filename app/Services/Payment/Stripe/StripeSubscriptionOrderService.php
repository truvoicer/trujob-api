<?php

namespace App\Services\Payment\Stripe;

use App\Enums\Order\OrderItemable;
use App\Enums\Payment\PaymentGateway;
use App\Enums\Price\PriceType;
use App\Enums\Subscription\SubscriptionIntervalUnit;
use App\Enums\Subscription\SubscriptionTenureType;
use App\Enums\Transaction\TransactionPaymentStatus;
use App\Enums\Transaction\TransactionStatus;
use App\Exceptions\PaymentGateway\StripeRequestException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\Payment\Stripe\Middleware\Checkout\StripeCheckoutSessionBuilder;
use App\Services\Payment\Stripe\Middleware\Checkout\StripeSubscriptionDataBuilder;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;

class StripeSubscriptionOrderService extends StripeBaseOrderService
{

    public function createProductSubscription(OrderItem $item): Session
    {

        $product = $item->orderItemable;
        if (!$product instanceof Product) {
            throw new \Exception('Product not found for order item');
        }
        $price = $product->prices->first();
        if (!$price) {
            throw new \Exception('Price not found for product');
        }
        $trialItem = $price->subscription->items
            ->where('tenure_type', SubscriptionTenureType::TRIAL->value)
            ->first();

        $shippingAddress = $item->order->shippingAddress;

        if (!$shippingAddress) {
            throw new \Exception('Shipping address not found for order item');
        }

        $stripePaymentGateway = $this->site->activePaymentGatewayByName(PaymentGateway::STRIPE)
            ->first()?->pivot ?? null;
        if (!$stripePaymentGateway) {
            throw new \Exception('Stripe payment gateway not found');
        }

        $return_url = $stripePaymentGateway->settings['return_url'] ?? null;
        try {
            $subscriptionBuilder = StripeSubscriptionDataBuilder::make()
                ->setDescription($product->description);
            if ($trialItem) {
                $subscriptionBuilder->setTrialPeriodDays(
                    match ($trialItem->frequency_interval_unit) {
                        SubscriptionIntervalUnit::DAY => $trialItem->total_cycles,
                        SubscriptionIntervalUnit::WEEK => $trialItem->total_cycles * 7,
                        SubscriptionIntervalUnit::MONTH => $trialItem->total_cycles * 30,
                        SubscriptionIntervalUnit::YEAR => $trialItem->total_cycles * 365,
                        default => throw new \Exception('Invalid frequency interval unit'),
                    }

                );
                $subscriptionBuilder->setTrialEnd(
                    match ($trialItem->frequency_interval_unit) {
                        SubscriptionIntervalUnit::DAY => now()->addDays($trialItem->total_cycles)->timestamp,
                        SubscriptionIntervalUnit::WEEK => now()->addWeeks($trialItem->total_cycles)->timestamp,
                        SubscriptionIntervalUnit::MONTH => now()->addMonths($trialItem->total_cycles)->timestamp,
                        SubscriptionIntervalUnit::YEAR => now()->addYears($trialItem->total_cycles)->timestamp,
                        default => throw new \Exception('Invalid frequency interval unit'),
                    }
                );
            }

            return $this->stripeCheckoutService->createSubscriptionSession(
                StripeCheckoutSessionBuilder::make()
                ->setBillingAddressCollection('required')
                // TODO: Implement shipping address collection if needed
                // ->setShippingAddressCollection() 
                    ->setLineItems([
                        [
                            'price_data' => [
                                'currency' => $price->currency->code,
                                'product_data' => [
                                    'name' => $product->title,
                                    'description' => $product->description,
                                ],
                                'unit_amount' => $item->calculateTotalPrice() * 100, // Amount in cents
                                'recurring' => [
                                    'interval' => strtolower($trialItem->frequency_interval_unit->value),
                                    'interval_count' => $trialItem->frequency_interval_count,
                                ],
                            ],
                            'quantity' => $item->quantity,
                        ],
                    ])
                    ->setUiMode('custom')
                    ->setReturnUrl(
                        ($return_url) ? $return_url : null
                    )
            );
        } catch (\Exception $e) {
            throw new \Exception('Failed to create Stripe subscription checkout session: ' . $e->getMessage());
        }
    }

    public function createOrderItemSubscription(OrderItem $item): Session
    {
        switch ($item->order_itemable_type) {
            case OrderItemable::PRODUCT:
                return $this->createProductSubscription($item);
        }

        throw new \Exception('Invalid order item type');
    }

    public function createSubscription(Order $order, Transaction $transaction): Session|false
    {
        $this->orderTransactionService->setUser($this->user);
        $this->orderTransactionService->setSite($this->site);

        $order = $order->loadOrderItemsByPriceType(
            $order->price_type,
            $this->user
        );

        $this->initializeStripeService();

        $item = $order->items->first();
        if (!$item) {
            throw new \Exception('No order items found for order');
        }
        $order->setPriceType(PriceType::SUBSCRIPTION);
        $order->init();
        $finalTotal = $order->calculateTotalPrice();
        $currencyCode = $order->currency?->code;

        try {
            $response = $this->createOrderItemSubscription($item);
            $this->orderTransactionService->updateTransaction(
                $order,
                $transaction,
                [
                    'currency_code' => $currencyCode,
                    'status' => TransactionStatus::PROCESSING,
                    'payment_status' => TransactionPaymentStatus::UNPAID,
                    'amount' => $finalTotal,
                    'order_data' => $response->toArray(),
                ]
            );
            return $response;
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


    public function handleSubscriptionApproval(Order $order, Transaction $transaction, array $data)
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

    public function handleSubscriptionCancel(Order $order, Transaction $transaction, array $data): bool
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
