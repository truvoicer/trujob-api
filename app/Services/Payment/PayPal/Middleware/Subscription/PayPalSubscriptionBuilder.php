<?php

namespace App\Services\Payment\PayPal\Middleware\Subscription;

use App\Services\Payment\PayPal\Middleware\PayPalAddressBuilder;
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
     * @param PayPalAddressBuilder $shippingAddress
     * @return $this
     */
    public function setSubscriber(
        string $email,
        string $nameGiven,
        string $nameSurname,
        PayPalAddressBuilder $shippingAddress
    ): self {
        $this->data['subscriber'] = [
            'name' => [
                'given_name' => $nameGiven,
                'surname' => $nameSurname,
            ],
            'email_address' => $email,
        ];

        $shippingAddress->validate();

        $this->data['subscriber']['shipping_address'] = [
            'name' => [
                'full_name' => $nameGiven . ' ' . $nameSurname
            ],
            'address' => [
                'address_line_1' => $shippingAddress->getAddressLine1(),
                'address_line_2' => $shippingAddress->getAddressLine2(),
                'admin_area_2' => $shippingAddress->getAdminArea2(),
                'admin_area_1' => $shippingAddress->getAdminArea1(),
                'postal_code' => $shippingAddress->getPostalCode(),
                'country_code' => $shippingAddress->getCountryCode(),
            ],
        ];

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
