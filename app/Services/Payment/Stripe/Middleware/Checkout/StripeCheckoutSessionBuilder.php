<?php

namespace App\Services\Payment\Stripe\Middleware\Checkout;

/**
 * StripeCheckoutSessionBuilder Class
 *
 * This class provides a fluent interface for building the parameters
 * required to create a Stripe Checkout Session. It allows you to set
 * various session properties incrementally and then retrieve the
 * final array of parameters for the Stripe API call.
 */
class StripeCheckoutSessionBuilder
{
    /**
     * The parameters array for the Stripe Checkout Session.
     *
     * @var array
     */
    protected $params = [];

    /**
     * Constructor for the CheckoutSessionBuilder.
     * Initializes with default mode as 'payment'.
     */
    public function __construct()
    {
        $this->params['mode'] = 'payment'; // Default mode
        $this->params['line_items'] = [];
    }

    /**
     * Sets the mode for the Checkout Session.
     *
     * @param string $mode Can be 'payment', 'setup', or 'subscription'.
     * @return self
     */
    public function setMode(string $mode): self
    {
        $this->params['mode'] = $mode;
        return $this;
    }

    public function setUiMode(string $uiMode): self
    {
        $this->params['ui_mode'] = $uiMode;
        return $this;
    }

    /**
     * Statically creates a new instance of the CheckoutSessionBuilder.
     * This allows for a more fluent static initiation.
     *
     * @return self
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * Sets the success URL for the Checkout Session.
     *
     * @param string $url The URL to redirect to after successful checkout.
     * @return self
     */
    public function setSuccessUrl(string $url): self
    {
        $this->params['success_url'] = $url;
        return $this;
    }

    /**
     * Sets the cancel URL for the Checkout Session.
     *
     * @param string $url The URL to redirect to if the user cancels checkout.
     * @return self
     */
    public function setCancelUrl(string $url): self
    {
        $this->params['cancel_url'] = $url;
        return $this;
    }

    /**
     * Sets the customer email for the Checkout Session.
     *
     * @param string $email The email address of the customer.
     * @return self
     */
    public function setCustomerEmail(string $email): self
    {
        $this->params['customer_email'] = $email;
        return $this;
    }

    /**
     * Sets the customer ID for the Checkout Session.
     *
     * @param string $customerId The ID of an existing Stripe Customer.
     * @return self
     */
    public function setCustomer(string $customerId): self
    {
        $this->params['customer'] = $customerId;
        return $this;
    }

    /**
     * Adds a single line item to the Checkout Session.
     *
     * @param array $lineItem An array representing a single line item.
     * Example: ['price' => 'price_123', 'quantity' => 1]
     * Or: ['price_data' => ['currency' => 'usd', 'product_data' => ['name' => 'Product'], 'unit_amount' => 1000], 'quantity' => 1]
     * @return self
     */
    public function addLineItem(array $lineItem): self
    {
        $this->params['line_items'][] = $lineItem;
        return $this;
    }

    /**
     * Sets multiple line items for the Checkout Session, overwriting any existing ones.
     *
     * @param array $lineItems An array of line item objects.
     * @return self
     */
    public function setLineItems(array $lineItems): self
    {
        $this->params['line_items'] = $lineItems;
        return $this;
    }

    /**
     * Adds metadata to the Checkout Session.
     *
     * @param array $metadata An associative array of key-value pairs.
     * @return self
     */
    public function setMetadata(array $metadata): self
    {
        $this->params['metadata'] = array_merge($this->params['metadata'] ?? [], $metadata);
        return $this;
    }

    /**
     * Sets the payment method types for the Checkout Session.
     *
     * @param array $paymentMethodTypes An array of payment method type strings (e.g., ['card', 'paypal']).
     * @return self
     */
    public function setPaymentMethodTypes(array $paymentMethodTypes): self
    {
        $this->params['payment_method_types'] = $paymentMethodTypes;
        return $this;
    }

    public function setRedirectOnCompletion(?string $redirectOnCompletion = null): self
    {
        if ($redirectOnCompletion) {
            $this->params['redirect_on_completion'] = $redirectOnCompletion;
        }
        return $this;
    }

    public function setReturnUrl(?string $returnUrl = null): self
    {
        if ($returnUrl) {
            $this->params['return_url'] = $returnUrl;
        }
        return $this;
    }


    /**
     * Sets the subscription data for subscription mode.
     *
     * @param array $subscriptionData An array of subscription data (e.g., ['trial_period_days' => 7]).
     * @return self
     */
    public function setSubscriptionData(array $subscriptionData): self
    {
        $this->params['subscription_data'] = array_merge($this->params['subscription_data'] ?? [], $subscriptionData);
        return $this;
    }

    /**
     * Sets the client reference ID for the Checkout Session.
     *
     * @param string $clientReferenceId A unique string to reference the Checkout Session on your end.
     * @return self
     */
    public function setClientReferenceId(string $clientReferenceId): self
    {
        $this->params['client_reference_id'] = $clientReferenceId;
        return $this;
    }

    /**
     * Sets the billing address collection mode.
     *
     * @param string $mode 'auto' or 'required'.
     * @return self
     */
    public function setBillingAddressCollection(string $mode): self
    {
        $this->params['billing_address_collection'] = $mode;
        return $this;
    }

    /**
     * Sets the shipping address collection mode.
     *
     * @param array $allowedCountryCodes An array of allowed 2 letter country codes for shipping addresses.
     * @return self
     */
    public function setShippingAddressCollection(array $allowedCountryCodes): self
    {
        $this->params['shipping_address_collection'] = [
            'allowed_countries' => $allowedCountryCodes,
        ];
        return $this;
    }

    /**
     * Adds any custom parameter to the Checkout Session.
     * Use this for parameters not explicitly covered by other methods.
     *
     * @param string $key The parameter key.
     * @param mixed $value The parameter value.
     * @return self
     */
    public function addParameter(string $key, $value): self
    {
        $this->params[$key] = $value;
        return $this;
    }

    /**
     * Builds and returns the final array of parameters for creating a Checkout Session.
     *
     * @return array
     * @throws \InvalidArgumentException If required parameters are missing.
     */
    public function build(): array
    {
        switch ($this->params['ui_mode']) {
            case 'custom':
                # code...
                break;

            default:
                if (!isset($this->params['success_url'])) {
                    throw new \InvalidArgumentException('Checkout Session success_url is required.');
                }
                if (!isset($this->params['cancel_url'])) {
                    throw new \InvalidArgumentException('Checkout Session cancel_url is required.');
                }
                break;
        }

        // Basic validation for required parameters
        if (!isset($this->params['mode'])) {
            throw new \InvalidArgumentException('Checkout Session mode is required.');
        }
        if (empty($this->params['line_items']) && $this->params['mode'] !== 'setup') {
            throw new \InvalidArgumentException('Line items are required for payment or subscription mode.');
        }

        return $this->params;
    }
}
