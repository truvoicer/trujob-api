<?php

namespace App\Services\Payment\PayPal;

use App\Enums\Order\OrderItemable;
use App\Enums\Payment\PaymentGatewayEnvironment;
use App\Enums\Price\PriceType;
use App\Enums\Transaction\TransactionPaymentStatus;
use App\Enums\Transaction\TransactionStatus;
use App\Exceptions\Product\ProductHealthException;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Price;
use App\Models\Product;
use App\Models\Transaction;
use App\Services\BaseService;
use App\Services\Order\Transaction\OrderTransactionService;
use App\Services\Payment\PayPal\Middleware\Billing\PayPalBillingCycleBuilder;
use App\Services\Payment\PayPal\Middleware\Billing\PayPalBillingPlanBuilder;
use App\Services\Payment\PayPal\Middleware\Billing\PayPalBillingPlanService;
use App\Services\Payment\PayPal\Middleware\PayPalAddressBuilder;
use App\Services\Payment\PayPal\Middleware\Product\PayPalProductBuilder;
use App\Services\Payment\PayPal\Middleware\Product\PayPalProductService;
use App\Services\Payment\PayPal\Middleware\Subscription\PayPalSubscriptionBuilder;
use App\Services\Payment\PayPal\Middleware\Subscription\PayPalSubscriptionResponseHandler;
use App\Services\Payment\PayPal\Middleware\Subscription\PayPalSubscriptionService;

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
            throw new \Exception('Failed to create PayPal product');
            //     $this->payPalProductService->getResponseData()
        }
    }

    public function createPayPalBillingPlan(
        string $paypalProductId,
        Price $price
    ) {
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

        try {
            return $this->billingPlanService->createPlan($billingPlanBuilder);
        } catch (\Exception $e) {
            // Handle exceptions as needed
            throw new \Exception('Failed to create PayPal billing plan');
            // $this->billingPlanService->getResponseData()
        }
    }

    public function createPayPalSubscriptionsByProduct(
        string $paypalProductId,
        OrderItem $item
    ): PayPalSubscriptionResponseHandler {
        $product = $item->orderItemable;
        if (!$product instanceof Product) {
            throw new \Exception('Product not found for order item');
        }
        $price = $product->prices->first();
        $createBillingPlan = $this->createPayPalBillingPlan(
            $paypalProductId,
            $price
        );

        $planId = $createBillingPlan['id']; // Replace with an actual plan ID

        $shippingAddress = $item->order->shippingAddress;

        if (!$shippingAddress) {
            throw new \Exception('Shipping address not found for order item');
        }
        $startTime = now()->addMinutes(5)->toISOString();
        $subscriptionBuilder = PayPalSubscriptionBuilder::build()
            ->setPlanId($planId)
            ->setStartTime($startTime)
            ->setQuantity($item->quantity)
            ->setSubscriber(
                $this->user->email,
                $this->user->first_name,
                $this->user->last_name,
                PayPalAddressBuilder::build()
                    ->setAddressLine1($shippingAddress->address_line_1)
                    ->setAddressLine2($shippingAddress->address_line_2)
                    ->setAdminArea1($shippingAddress->city)
                    ->setAdminArea2($shippingAddress->state)
                    ->setPostalCode($shippingAddress->postal_code)
                    ->setCountryCode($shippingAddress->country->iso2)
            );
        try {
            return $this->payPalSubscriptionService->createSubscription(
                $subscriptionBuilder
            );
        } catch (\Exception $e) {
            throw new \Exception('Failed to create PayPal subscription');
            // $this->payPalSubscriptionService->getResponseData()
        }
    }

    public function createProductSubscription(OrderItem $item): PayPalSubscriptionResponseHandler
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

        $createPayPalProduct = $this->createPayPalProduct($product);

        $productId = $createPayPalProduct['id'];

        return $this->createPayPalSubscriptionsByProduct(
            $productId,
            $item
        );
    }

    public function createOrderItemSubscription(OrderItem $item): PayPalSubscriptionResponseHandler
    {
        switch ($item->order_itemable_type) {
            case OrderItemable::PRODUCT:
                return $this->createProductSubscription($item);
        }

        throw new \Exception('Invalid order item type');
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

    public function createSubscription(Order $order, Transaction $transaction): PayPalSubscriptionResponseHandler
    {
        $this->orderTransactionService->setUser($this->user);
        $this->orderTransactionService->setSite($this->site);

        $order = $order->loadOrderItemsByPriceType(
            $order->price_type,
            $this->user
        );

        $this->initializePayPalService();

        $item = $order->items->first();
        if (!$item) {
            throw new \Exception('No order items found for order');
        }
        $order->setPriceType(PriceType::SUBSCRIPTION);
        $order->init();
        $finalTotal = $order->calculateTotalPrice();
        $response = $this->createOrderItemSubscription($item);

        $currencyCode = $order->currency?->code;
        if (!$response->isSuccess()) {
            $errorMessage = $response->getErrorMessage();
            $errorDetails = $response->getErrorDetails();
            $this->orderTransactionService->updateTransaction(
                $order,
                $transaction,
                [
                    'currency_code' => $currencyCode,
                    'status' => TransactionStatus::FAILED,
                    'payment_status' => TransactionPaymentStatus::UNPAID,
                    'amount' => $finalTotal,
                    'order_data' => $response->getResponseData(),
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
                'payment_status' => TransactionPaymentStatus::UNPAID,
                'amount' => $finalTotal,
                'order_data' => $response->getResponseData(),
            ]
        );


        return $response;
    }

    public function handleSubscriptionApproval(Order $order, Transaction $transaction, array $data): PayPalSubscriptionResponseHandler
    {
        //         {
        //     "orderID": "3GE228659M104945X",
        //     "subscriptionID": "I-NCH9FU5V8KNC",
        //     "facilitatorAccessToken": "A21AAKP1h25im1yU5RCa6jubcbJxl9EQdwV9dXCnp-nh_7sp0L9ytBN6KNs-5-q7OZIHE-uD86v_ppznF7G7HFXGjLiAYO41g",
        //     "paymentSource": "paypal"
        // }
        $this->orderTransactionService->setUser($this->user);
        $this->orderTransactionService->setSite($this->site);

        $this->initializePayPalService();

        if (empty($data['subscriptionID'])) {
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

        $response = $this->payPalSubscriptionService->showSubscription(
            $data['subscriptionID']
        );
        if (!$response->isSuccess()) {
            $this->orderTransactionService->updateTransaction(
                $order,
                $transaction,
                [
                    'status' => TransactionStatus::FAILED,
                    'payment_status' => TransactionPaymentStatus::UNPAID,
                    'transaction_data' => $response->getResponseData(),
                ]
            );
            throw new \Exception(
                'Error retrieving PayPal subscription: ' . json_encode($response->getResponse())
            );
        }
        $this->orderTransactionService->updateTransaction(
            $order,
            $transaction,
            [
                'status' => TransactionStatus::COMPLETED,
                'payment_status' => TransactionPaymentStatus::PAID,
                'transaction_data' => $response->getResponseData(),
            ]
        );
        return $response;
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
