<?php

namespace App\Services\Payment\PayPal\Subscription;

use InvalidArgumentException;

/**
 * Class PayPalSubscriptionBuilder
 *
 * A fluent builder for constructing PayPal subscription data arrays.
 */
class PayPalSubscriptionBuilder
{
    /**
     * @var array The subscription data being built.
     */
    protected array $data = [];

    /**
     * Private constructor to enforce static `build()` method usage.
     */
    private function __construct()
    {
        $this->data = [];
    }

    /**
     * Static factory method to start building a new subscription.
     *
     * @return static
     */
    public static function build(): static
    {
        return new static();
    }

    /**
     * Sets the plan ID for the subscription.
     *
     * @param string $planId The ID of the billing plan.
     * @return $this
     */
    public function setPlanId(string $planId): self
    {
        $this->data['plan_id'] = $planId;
        return $this;
    }

    /**
     * Sets the start time of the subscription.
     *
     * @param string $startTime The date and time when the subscription starts, in ISO 8601 format (e.g., "2023-01-01T00:00:00Z").
     * @return $this
     */
    public function setStartTime(string $startTime): self
    {
        $this->data['start_time'] = $startTime;
        return $this;
    }

    /**
     * Sets the quantity of the product in the subscription.
     *
     * @param int $quantity The quantity of the product.
     * @return $this
     * @throws InvalidArgumentException If quantity is not positive.
     */
    public function setQuantity(int $quantity): self
    {
        if ($quantity <= 0) {
            throw new InvalidArgumentException("Quantity must be a positive integer.");
        }
        $this->data['quantity'] = $quantity;
        return $this;
    }

    /**
     * Sets the shipping amount for the subscription.
     *
     * @param string $currencyCode The currency code (e.g., 'USD').
     * @param string $value The value of the shipping amount.
     * @return $this
     */
    public function setShippingAmount(string $currencyCode, string $value): self
    {
        $this->data['shipping_amount'] = [
            'currency_code' => $currencyCode,
            'value' => $value,
        ];
        return $this;
    }

    /**
     * Sets the subscriber details.
     *
     * @param string $email The subscriber's email address.
     * @param string $nameGiven The subscriber's given name.
     * @param string $nameSurname The subscriber's surname.
     * @param array $shippingAddress Optional shipping address details.
     * @return $this
     */
    public function setSubscriber(string $email, string $nameGiven, string $nameSurname, array $shippingAddress = []): self
    {
        $this->data['subscriber'] = [
            'name' => [
                'given_name' => $nameGiven,
                'surname' => $nameSurname,
            ],
            'email_address' => $email,
        ];

        if (!empty($shippingAddress)) {
            // Basic validation for shipping address structure
            if (!isset($shippingAddress['address_line_1']) || !isset($shippingAddress['admin_area_2']) || !isset($shippingAddress['admin_area_1']) || !isset($shippingAddress['postal_code']) || !isset($shippingAddress['country_code'])) {
                throw new InvalidArgumentException("Shipping address must contain address_line_1, admin_area_2, admin_area_1, postal_code, and country_code.");
            }
            $this->data['subscriber']['shipping_address'] = [
                'address' => $shippingAddress,
            ];
        }
        return $this;
    }

    /**
     * Sets the application context for the subscription.
     *
     * @param string $returnUrl The URL to which the customer is redirected after approving the subscription.
     * @param string $cancelUrl The URL to which the customer is redirected after canceling the subscription.
     * @param string $brandName The label that overrides the business name in the PayPal checkout page.
     * @param string $locale The BCP 47 language tag to localize the checkout flow.
     * @param string $landingPage The type of landing page to show on the PayPal site for customer checkout.
     * @param string $shippingPreference The shipping preference.
     * @param string $userAction The user action.
     * @param array $paymentMethod Optional payment method details.
     * @return $this
     */
    public function setApplicationContext(
        string $returnUrl,
        string $cancelUrl,
        string $brandName = '',
        string $locale = 'en-US',
        string $landingPage = 'LOGIN', // Possible values: LOGIN, BILLING, NO_PREFERENCE
        string $shippingPreference = 'SET_PROVIDED_ADDRESS', // Possible values: GET_FROM_FILE, NO_SHIPPING, SET_PROVIDED_ADDRESS
        string $userAction = 'SUBSCRIBE_NOW', // Possible values: CONTINUE, PAY_NOW, SUBSCRIBE_NOW
        array $paymentMethod = [] // Example: ['payer_selected_funding_instrument' => 'PAYPAL']
    ): self {
        $this->data['application_context'] = [
            'return_url' => $returnUrl,
            'cancel_url' => $cancelUrl,
            'brand_name' => $brandName,
            'locale' => $locale,
            'landing_page' => $landingPage,
            'shipping_preference' => $shippingPreference,
            'user_action' => $userAction,
        ];

        if (!empty($paymentMethod)) {
            $this->data['application_context']['payment_method'] = $paymentMethod;
        }

        return $this;
    }

    /**
     * Sets the custom ID for the subscription.
     *
     * @param string $customId The custom ID for the subscription.
     * @return $this
     */
    public function setCustomId(string $customId): self
    {
        $this->data['custom_id'] = $customId;
        return $this;
    }

    /**
     * Sets the external ID for the subscription.
     *
     * @param string $externalId The external ID for the subscription.
     * @return $this
     */
    public function setExternalId(string $externalId): self
    {
        $this->data['external_id'] = $externalId;
        return $this;
    }

    /**
     * Sets the auto renewal flag.
     *
     * @param bool $autoRenewal True to enable auto renewal, false otherwise.
     * @return $this
     */
    public function setAutoRenewal(bool $autoRenewal): self
    {
        $this->data['auto_renewal'] = $autoRenewal;
        return $this;
    }

    /**
     * Returns the built subscription data array.
     *
     * @return array
     * @throws InvalidArgumentException If required fields are missing.
     */
    public function get(): array
    {
        if (!isset($this->data['plan_id'])) {
            throw new InvalidArgumentException("Subscription plan ID is required.");
        }
        if (!isset($this->data['subscriber'])) {
            throw new InvalidArgumentException("Subscriber details are required.");
        }

        return $this->data;
    }
}
