<?php

namespace App\Services\Payment\PayPal;

use App\Services\BaseService;
use PaypalServerSdkLib\Models\Builders\BillingCycleBuilder;
use PaypalServerSdkLib\Models\Builders\FrequencyBuilder;
use PaypalServerSdkLib\Models\Builders\MoneyBuilder;
use PaypalServerSdkLib\Models\Builders\PaymentPreferencesBuilder;
use PaypalServerSdkLib\Models\Builders\PlanBuilder;
use PaypalServerSdkLib\Models\Builders\ProductBuilder;
use PaypalServerSdkLib\Models\Builders\PricingSchemeBuilder;
use PaypalServerSdkLib\Models\Builders\SubscriptionBuilder;
use PaypalServerSdkLib\Models\Builders\SubscriberBuilder;
use PaypalServerSdkLib\Models\Builders\ShippingAddressBuilder;
use PaypalServerSdkLib\Models\ProductType;
use PaypalServerSdkLib\Models\CurrencyCode;
use PaypalServerSdkLib\Models\FrequencyType;
use PaypalServerSdkLib\Models\TenureType;
use PaypalServerSdkLib\Models\SubscriptionStatus;
use PaypalServerSdkLib\Models\PatchRequest;
use PaypalServerSdkLib\Models\Patch;
use PaypalServerSdkLib\Models\Operation;

/**
 * Class PayPalSubscriptionService
 *
 * Handles interactions with PayPal's Subscriptions API.
 */
class PayPalSubscriptionService extends BaseService
{
    public function __construct(
        private PayPalService $payPalService
    ) {
        parent::__construct();
    }

    /**
     * Initializes the underlying PayPalService with credentials and environment.
     * This method is crucial and should be called before any PayPal API operations.
     * You might call this in your controller or another orchestrating service.
     */
    public function initializePayPalService(): void
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
        $this->payPalService->init(); // Initialize the PayPal SDK client and get token
    }

    /**
     * Creates a PayPal Product.
     * A product represents the item or service that is being subscribed to.
     *
     * @param string $name The name of the product.
     * @param string $description The description of the product.
     * @param string $type The type of product (e.g., 'SERVICE', 'DIGITAL', 'PHYSICAL').
     * Use constants from PaypalServerSdkLib\Models\ProductType.
     * @param string $category The category of the product.
     * @param string|null $imageUrl An optional URL for the product image.
     * @param string|null $homeUrl An optional URL for the product's home page.
     * @return array The PayPal Product response data.
     * @throws \Exception If product creation fails.
     */
    public function createProduct(
        string $name,
        string $description,
        string $type = ProductType::SERVICE,
        string $category = 'SOFTWARE',
        ?string $imageUrl = null,
        ?string $homeUrl = null
    ): array {
        $this->initializePayPalService();
        $productBuilder = ProductBuilder::init($name, $description, $type);
        $productBuilder->category($category);

        if ($imageUrl) {
            $productBuilder->imageUrl($imageUrl);
        }
        if ($homeUrl) {
            $productBuilder->homeUrl($homeUrl);
        }

        try {
            // Corrected method call: use products() instead of getProductsController()
            $response = $this->payPalService->getClient()->products()->createProduct(
                $productBuilder->build()
            );

            if (!$response->isSuccess() || $response->getStatusCode() !== 201) {
                throw new \Exception('Failed to create PayPal product: ' . $response->getRawBody());
            }

            return (array) $response->getResult();
        } catch (\Exception $e) {
            throw new \Exception('Error creating PayPal product: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Creates a PayPal Plan.
     * A plan defines the billing cycle, pricing, and other details for a subscription.
     *
     * @param string $productId The ID of the PayPal Product this plan is for.
     * @param string $name The name of the plan.
     * @param string $description The description of the plan.
     * @param string $intervalUnit The unit of the billing interval (e.g., 'DAY', 'WEEK', 'MONTH', 'YEAR').
     * Use constants from PaypalServerSdkLib\Models\FrequencyType.
     * @param int $intervalCount The number of interval units (e.g., 1 for 'every month').
     * @param string $currencyCode The currency code (e.g., 'USD', 'GBP').
     * @param string $value The price per billing cycle.
     * @param bool $autoBillOutstanding True to automatically bill outstanding amount, false otherwise.
     * @param int $maxFails Allowed failed payments before subscription is suspended.
     * @param string $setupFeeValue Optional setup fee value.
     * @return array The PayPal Plan response data.
     * @throws \Exception If plan creation fails.
     */
    public function createPlan(
        string $productId,
        string $name,
        string $description,
        string $intervalUnit, // e.g., FrequencyType::MONTH
        int $intervalCount,
        string $currencyCode, // e.g., CurrencyCode::GBP
        string $value,
        bool $autoBillOutstanding = true,
        int $maxFails = 3,
        ?string $setupFeeValue = null
    ): array {
        $this->initializePayPalService();

        $frequency = FrequencyBuilder::init($intervalUnit, $intervalCount)->build();
        $money = MoneyBuilder::init($currencyCode, $value)->build();
        $pricingScheme = PricingSchemeBuilder::init($money)->build();

        $billingCycle = BillingCycleBuilder::init($frequency, TenureType::REGULAR)
            ->sequence(1) // Usually 1 for the main billing cycle
            ->totalCycles(0) // 0 for infinite cycles (until cancelled)
            ->pricingScheme($pricingScheme)
            ->build();

        $paymentPreferences = PaymentPreferencesBuilder::init($autoBillOutstanding, $maxFails)
            ->setupFee(MoneyBuilder::init($currencyCode, $setupFeeValue ?? '0.00')->build())
            ->build();

        $planBuilder = PlanBuilder::init($productId, $name, $intervalUnit) // Use intervalUnit for the product_id parameter as per SDK builder
            ->description($description)
            ->billingCycles([$billingCycle])
            ->paymentPreferences($paymentPreferences);


        try {
            // Corrected method call: use billingPlans() instead of getBillingPlansController()
            $response = $this->payPalService->getClient()->billingPlans()->createPlan(
                $planBuilder->build()
            );

            if (!$response->isSuccess() || $response->getStatusCode() !== 201) {
                throw new \Exception('Failed to create PayPal plan: ' . $response->getRawBody());
            }

            return (array) $response->getResult();
        } catch (\Exception $e) {
            throw new \Exception('Error creating PayPal plan: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Creates a PayPal Subscription for a customer.
     *
     * @param string $planId The ID of the PayPal Plan to subscribe to.
     * @param array $subscriberData An array containing subscriber details (e.g., 'name', 'email_address', 'address').
     * Example: ['name' => ['given_name' => 'John', 'surname' => 'Doe'], 'email_address' => 'john.doe@example.com']
     * @param string $startDate The date and time when the subscription begins, in ISO 8601 format (e.g., "2025-07-20T00:00:00Z").
     * @param string|null $customId An optional custom ID for the subscription.
     * @param string|null $shippingAddressData Optional array for shipping address.
     * @return array The PayPal Subscription response data.
     * @throws \Exception If subscription creation fails.
     */
    public function createSubscription(
        string $planId,
        array $subscriberData,
        string $startDate,
        ?string $customId = null,
        ?array $shippingAddressData = null
    ): array {
        $this->initializePayPalService();

        $subscriberBuilder = SubscriberBuilder::init()
            ->name($subscriberData['name'] ?? null)
            ->emailAddress($subscriberData['email_address'] ?? null);

        // Build shipping address if provided
        $shippingAddress = null;
        if ($shippingAddressData) {
            $shippingAddressBuilder = ShippingAddressBuilder::init()
                ->addressLine1($shippingAddressData['address_line_1'] ?? null)
                ->adminArea2($shippingAddressData['admin_area_2'] ?? null)
                ->adminArea1($shippingAddressData['admin_area_1'] ?? null)
                ->postalCode($shippingAddressData['postal_code'] ?? null)
                ->countryCode($shippingAddressData['country_code'] ?? null)
                ->fullName($shippingAddressData['full_name'] ?? null);
            $shippingAddress = $shippingAddressBuilder->build();
        }

        $subscriptionBuilder = SubscriptionBuilder::init($planId, $startDate)
            ->subscriber($subscriberBuilder->build());

        if ($customId) {
            $subscriptionBuilder->customId($customId);
        }
        if ($shippingAddress) {
            $subscriptionBuilder->shippingAmount(
                MoneyBuilder::init($this->payPalService->getCurrencyCode() ?? CurrencyCode::GBP, '0.00')->build() // Assuming 0 shipping for now, adjust as needed
            );
            $subscriptionBuilder->shippingAddress($shippingAddress);
        }


        try {
            // Corrected method call: use subscriptions() instead of getSubscriptionsController()
            $response = $this->payPalService->getClient()->subscriptions()->createSubscription(
                $subscriptionBuilder->build()
            );

            if (!$response->isSuccess() || $response->getStatusCode() !== 201) {
                throw new \Exception('Failed to create PayPal subscription: ' . $response->getRawBody());
            }

            return (array) $response->getResult();
        } catch (\Exception $e) {
            throw new \Exception('Error creating PayPal subscription: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Activates a PayPal subscription.
     *
     * @param string $subscriptionId The ID of the subscription to activate.
     * @param string $reason The reason for activation.
     * @return bool True if activated successfully, false otherwise.
     * @throws \Exception If activation fails.
     */
    public function activateSubscription(string $subscriptionId, string $reason = 'Customer request'): bool
    {
        $this->initializePayPalService();
        try {
            // Corrected method call: use subscriptions() instead of getSubscriptionsController()
            $response = $this->payPalService->getClient()->subscriptions()->activateSubscription(
                $subscriptionId,
                ['reason' => $reason]
            );

            if (!$response->isSuccess() || $response->getStatusCode() !== 204) { // 204 No Content for success
                throw new \Exception('Failed to activate PayPal subscription: ' . $response->getRawBody());
            }

            return true;
        } catch (\Exception $e) {
            throw new \Exception('Error activating PayPal subscription: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Cancels a PayPal subscription.
     *
     * @param string $subscriptionId The ID of the subscription to cancel.
     * @param string $reason The reason for cancellation.
     * @return bool True if cancelled successfully, false otherwise.
     * @throws \Exception If cancellation fails.
     */
    public function cancelSubscription(string $subscriptionId, string $reason = 'Customer request'): bool
    {
        $this->initializePayPalService();
        try {
            // Corrected method call: use subscriptions() instead of getSubscriptionsController()
            $response = $this->payPalService->getClient()->subscriptions()->cancelSubscription(
                $subscriptionId,
                ['reason' => $reason]
            );

            if (!$response->isSuccess() || $response->getStatusCode() !== 204) { // 204 No Content for success
                throw new \Exception('Failed to cancel PayPal subscription: ' . $response->getRawBody());
            }

            return true;
        } catch (\Exception $e) {
            throw new \Exception('Error canceling PayPal subscription: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Retrieves details of a PayPal subscription.
     *
     * @param string $subscriptionId The ID of the subscription.
     * @return array The subscription details.
     * @throws \Exception If retrieval fails.
     */
    public function getSubscriptionDetails(string $subscriptionId): array
    {
        $this->initializePayPalService();
        try {
            // Corrected method call: use subscriptions() instead of getSubscriptionsController()
            $response = $this->payPalService->getClient()->subscriptions()->getSubscription(
                $subscriptionId
            );

            if (!$response->isSuccess() || $response->getStatusCode() !== 200) {
                throw new \Exception('Failed to get PayPal subscription details: ' . $response->getRawBody());
            }

            return (array) $response->getResult();
        } catch (\Exception $e) {
            throw new \Exception('Error getting PayPal subscription details: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Updates a PayPal subscription.
     * This method allows for partial updates using JSON Patch.
     *
     * @param string $subscriptionId The ID of the subscription to update.
     * @param array $patchOperations An array of patch operations. Each operation should be an array with 'op', 'path', and 'value'.
     * Example: [['op' => 'replace', 'path' => '/billing_info/next_billing_time', 'value' => '2025-08-01T00:00:00Z']]
     * See PayPal API documentation for valid patch paths and operations.
     * @return bool True if updated successfully, false otherwise.
     * @throws \Exception If update fails.
     */
    public function updateSubscription(string $subscriptionId, array $patchOperations): bool
    {
        $this->initializePayPalService();

        $patches = [];
        foreach ($patchOperations as $op) {
            $patch = Patch::init($op['op'], $op['path']);
            if (isset($op['value'])) {
                $patch->value($op['value']);
            }
            // Add from and other fields if needed for specific operations
            $patches[] = $patch;
        }

        $patchRequest = PatchRequest::init($patches);

        try {
            // Corrected method call: use subscriptions() instead of getSubscriptionsController()
            $response = $this->payPalService->getClient()->subscriptions()->updateSubscription(
                $subscriptionId,
                $patchRequest->build()
            );

            if (!$response->isSuccess() || $response->getStatusCode() !== 204) { // 204 No Content for success
                throw new \Exception('Failed to update PayPal subscription: ' . $response->getRawBody());
            }

            return true;
        } catch (\Exception $e) {
            throw new \Exception('Error updating PayPal subscription: ' . $e->getMessage(), 0, $e);
        }
    }
}
