<?php

namespace App\Services\Payment\PayPal;

use App\Enums\Order\OrderItemable;
use App\Enums\Payment\PaymentGatewayEnvironment;
use App\Enums\Price\PriceType;
use App\Enums\Transaction\TransactionStatus;
use App\Exceptions\Product\ProductHealthException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\BaseService;
use App\Services\Order\Transaction\OrderTransactionService;
use App\Services\Payment\PayPal\Billing\PayPalBillingCycleBuilder;
use App\Services\Payment\PayPal\Billing\PayPalBillingPlanBuilder;
use App\Services\Payment\PayPal\Billing\PayPalBillingPlanService;
use App\Services\Payment\PayPal\Product\PayPalProductBuilder;
use App\Services\Payment\PayPal\Product\PayPalProductService;
use App\Services\Payment\PayPal\Subscription\PayPalSubscriptionBuilder;
use App\Services\Payment\PayPal\Subscription\PayPalSubscriptionService;

class PayPalSubscriptionOrderService extends BaseService
{


    public function __construct(
        private PayPalSubscriptionService $payPalSubscriptionService,
        private PayPalProductService $payPalProductService,
        private PayPalBillingPlanService $billingPlanService,
        private OrderTransactionService $orderTransactionService,
    ) {
        // Initialize any PayPal SDK or configuration here
        parent::__construct();
    }

    public function createPayPalProduct(Product $product)
    {
        try {
            $productBuilder = PayPalProductBuilder::build()
                ->setName($product->title)
                ->setType(strtoupper($product->type->value))
                // ->setCategory($product->categories->first()?->name ?? 'General')
                ->setDescription($product->description)
                ->setImageUrl('https://example.com/ebook_image.jpg')
                ->setHomeUrl('https://example.com/premium-ebooks');

            return $this->payPalProductService->createProduct($productBuilder);
        } catch (\Exception $e) {
            dd($this->payPalProductService->getResponseData());
        }
    }

    public function createPayPalBillingPlan(
        string $paypalProductId,
        Product $product
    ) {
        foreach ($product->prices as $price) {
            if (!$price->subscription->exists()) {
                throw new \Exception('Subscription not found for product price');
            }
            if ($price->subscription->items->isEmpty()) {
                throw new \Exception('No subscription items found for product price');
            }
            $billingPlanBuilder = PayPalBillingPlanBuilder::build()
                ->setProductId($paypalProductId)
                ->setName($price->subscription->label)
                ->setDescription($price->subscription->description)
                ->setType($price->subscription->type->value);
            if ($price->subscription->has_setup_fee) {
                $billingPlanBuilder->setSetupFee(
                    $price->subscription->setup_fee_value,
                    $price->subscription->setupFeeCurrency->code
                );
            }
            $billingPlanBuilder->setPaymentPreferences(
                $price->subscription->auto_bill_outstanding,
                $price->subscription->setup_fee_failure_action->value,
                $price->subscription->payment_failure_threshold,
            );

            foreach ($price->subscription->items as $priceItem) {
                // dd($priceItem->sequence);
                // Add a trial billing cycle
                $trialCycle = PayPalBillingCycleBuilder::build()
                    ->setFrequency(
                        $priceItem->frequency_interval_unit,
                        $priceItem->frequency_interval_count,
                    )
                    ->setTenureType($priceItem->tenure_type)
                    ->setSequence($priceItem->sequence)
                    ->setTotalCycles($priceItem->total_cycles)
                    ->setPricingScheme(
                        $priceItem->priceCurrency->code,
                        $priceItem->price_value,
                    );

                $billingPlanBuilder->addBillingCycle($trialCycle);
            }
        }
        try {
            return $this->billingPlanService->createPlan($billingPlanBuilder);
        } catch (\Exception $e) {
            // Handle exceptions as needed
            dd($this->billingPlanService->getResponseData());
        }
    }

    public function createProductSubscription(Product $product)
    {
        $healthCheckData = $product->healthCheck();
        if ($healthCheckData['unhealthy']['count'] > 0) {
            throw new ProductHealthException(
                $product,
                $healthCheckData
            );
        }

        $createPayPalProduct = $this->createPayPalProduct($product);

        $productId = $createPayPalProduct['id'];

        $createBillingPlan = $this->createPayPalBillingPlan(
            $productId,
            $product
        );

        $planId = $createBillingPlan['id']; // Replace with an actual plan ID

        $subscriptionBuilder = PayPalSubscriptionBuilder::build()
            ->setPlanId($planId)
            ->setStartTime(now()->addMinutes(5)->toIso8601ZuluString()) // Start 5 minutes from now
            ->setQuantity(1)
            ->setSubscriber(
                'customer@example.com',
                'John',
                'Doe',
                [
                    'address_line_1' => '123 Main St',
                    'admin_area_2' => 'San Jose',
                    'admin_area_1' => 'CA',
                    'postal_code' => '95131',
                    'country_code' => 'US',
                ]
            )
            ->setApplicationContext(
                'https://your-app.com/paypal/return',
                'https://your-app.com/paypal/cancel',
                'Your Brand Name',
                'en-US',
                'LOGIN',
                'SET_PROVIDED_ADDRESS',
                'SUBSCRIBE_NOW'
            );

        $subscription = $this->payPalSubscriptionService->createSubscription($subscriptionBuilder);





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

    public function createOrderItemSubscription(OrderItem $item): Item|null
    {
        switch ($item->order_itemable_type) {
            case OrderItemable::PRODUCT:
                $product = $item->orderItemable;
                if (!$product instanceof Product) {
                    throw new \Exception('Product not found for order item');
                }
                return $this->createProductSubscription($product);
                break;
        }
        return null;
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
        $this->payPalSubscriptionService->setSandboxMode(
            $environment === PaymentGatewayEnvironment::SANDBOX
        );
        $this->payPalSubscriptionService->setCredentials(
            $clientId,
            $clientSecret
        );

        $this->payPalProductService->setSandboxMode(
            $environment === PaymentGatewayEnvironment::SANDBOX
        );
        $this->payPalProductService->setCredentials(
            $clientId,
            $clientSecret
        );

        $this->billingPlanService->setSandboxMode(
            $environment === PaymentGatewayEnvironment::SANDBOX
        );
        $this->billingPlanService->setCredentials(
            $clientId,
            $clientSecret
        );
    }

    public function createSubscription(Order $order, Transaction $transaction)
    {
        $this->orderTransactionService->setUser($this->user);
        $this->orderTransactionService->setSite($this->site);

        $getOrder = $order->loadOrderItemsByPriceType(
            $order->price_type,
            $this->user
        );

        $this->initializePayPalService();
        // $order->setPriceType($order->price_type);
        // $order->init();

        foreach ($getOrder->items as $item) {

            $item->setPriceType($order->price_type);
            $item->init();
            $orderItem = $this->createOrderItemSubscription($item);
            if (!$orderItem) {
                throw new \Exception('Error creating PayPal order item');
            }
        }

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
