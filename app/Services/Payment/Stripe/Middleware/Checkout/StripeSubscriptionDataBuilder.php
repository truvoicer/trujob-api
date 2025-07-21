<?php

namespace App\Services\Payment\Stripe\Middleware\Checkout;

/**
 * StripeSubscriptionDataBuilder Class
 *
 * This class provides a fluent interface for building the 'subscription_data'
 * parameters for a Stripe Checkout Session. It allows you to set various
 * subscription-related properties incrementally and then retrieve the
 * final array of parameters.
 */
class StripeSubscriptionDataBuilder
{
    /**
     * The parameters array for the 'subscription_data'.
     *
     * @var array
     */
    protected $params = [];

    /**
     * Constructor for the StripeSubscriptionDataBuilder.
     */
    public function __construct()
    {
        // Initialize with an empty array for parameters
        $this->params = [];
    }

    /**
     * Statically creates a new instance of the StripeSubscriptionDataBuilder.
     * This allows for a more fluent static initiation.
     *
     * @return self
     */
    public static function make(): self
    {
        return new self();
    }

    /**
     * Sets the trial period in days for the subscription.
     *
     * @param int $days The number of days for the trial period.
     * @return self
     */
    public function setTrialPeriodDays(int $days): self
    {
        $this->params['trial_period_days'] = $days;
        return $this;
    }

    /**
     * Sets the trial end timestamp for the subscription.
     *
     * @param int $timestamp A Unix timestamp representing the end of the trial period.
     * @return self
     */
    public function setTrialEnd(int $timestamp): self
    {
        $this->params['trial_end'] = $timestamp;
        return $this;
    }

    /**
     * Sets the ID of the default payment method for the subscription.
     *
     * @param string $paymentMethodId The ID of a PaymentMethod to attach to the subscription.
     * @return self
     */
    public function setDefaultPaymentMethod(string $paymentMethodId): self
    {
        $this->params['default_payment_method'] = $paymentMethodId;
        return $this;
    }

    /**
     * Sets the ID of the default source (deprecated, prefer PaymentMethod) for the subscription.
     *
     * @param string $sourceId The ID of a Source to attach to the subscription.
     * @return self
     */
    public function setDefaultSource(string $sourceId): self
    {
        $this->params['default_source'] = $sourceId;
        return $this;
    }

    /**
     * Sets an array of default tax rates for the subscription.
     *
     * @param array $taxRateIds An array of tax rate IDs.
     * @return self
     */
    public function setDefaultTaxRates(array $taxRateIds): self
    {
        $this->params['default_tax_rates'] = $taxRateIds;
        return $this;
    }

    /**
     * Sets the coupon code to apply to the subscription.
     *
     * @param string $couponId The ID of a coupon to apply.
     * @return self
     */
    public function setCoupon(string $couponId): self
    {
        $this->params['coupon'] = $couponId;
        return $this;
    }

    /**
     * Sets the promotion code to apply to the subscription.
     *
     * @param string $promotionCodeId The ID of a promotion code to apply.
     * @return self
     */
    public function setPromotionCode(string $promotionCodeId): self
    {
        $this->params['promotion_code'] = $promotionCodeId;
        return $this;
    }

    /**
     * Adds metadata to the subscription.
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
     * Sets the application fee percentage for Connect only.
     *
     * @param float $percent A non-negative decimal between 0 and 100, with at most two decimal places.
     * @return self
     */
    public function setApplicationFeePercent(float $percent): self
    {
        $this->params['application_fee_percent'] = $percent;
        return $this;
    }

    /**
     * Sets a future timestamp to anchor the subscription’s billing cycle for new subscriptions.
     *
     * @param int $timestamp A Unix timestamp.
     * @return self
     */
    public function setBillingCycleAnchor(int $timestamp): self
    {
        $this->params['billing_cycle_anchor'] = $timestamp;
        return $this;
    }

    /**
     * Sets the billing mode for the subscription.
     *
     * @param string $type Controls the calculation and orchestration of prorations and invoices.
     * Possible values: 'classic' or 'flexible'.
     * @return self
     */
    public function setBillingMode(string $type): self
    {
        $this->params['billing_mode'] = ['type' => $type];
        return $this;
    }

    /**
     * Sets the subscription’s description, meant to be displayable to the customer.
     *
     * @param string $description The description of the subscription.
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->params['description'] = $description;
        return $this;
    }

    /**
     * Sets the invoice settings for the subscription.
     *
     * @param array $settings An associative array of invoice settings.
     * @return self
     */
    public function setInvoiceSettings(array $settings): self
    {
        $this->params['invoice_settings'] = array_merge($this->params['invoice_settings'] ?? [], $settings);
        return $this;
    }

    /**
     * Sets the account on behalf of which to charge, for each of the subscription’s invoices.
     * (Connect only)
     *
     * @param string $accountId The ID of the connected account.
     * @return self
     */
    public function setOnBehalfOf(string $accountId): self
    {
        $this->params['on_behalf_of'] = $accountId;
        return $this;
    }

    /**
     * Determines how to handle prorations resulting from the billing_cycle_anchor.
     * Possible values: 'create_prorations' or 'none'.
     *
     * @param string $behavior The proration behavior.
     * @return self
     */
    public function setProrationBehavior(string $behavior): self
    {
        $this->params['proration_behavior'] = $behavior;
        return $this;
    }

    /**
     * Sets transfer data for the subscription's invoices. (Connect only)
     *
     * @param array $transferData An associative array of transfer data.
     * @return self
     */
    public function setTransferData(array $transferData): self
    {
        $this->params['transfer_data'] = array_merge($this->params['transfer_data'] ?? [], $transferData);
        return $this;
    }

    /**
     * Sets settings related to subscription trials.
     *
     * @param array $settings An associative array of trial settings.
     * @return self
     */
    public function setTrialSettings(array $settings): self
    {
        $this->params['trial_settings'] = array_merge($this->params['trial_settings'] ?? [], $settings);
        return $this;
    }

    /**
     * Adds any custom parameter to the subscription data.
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
     * Builds and returns the final array of parameters for 'subscription_data'.
     *
     * @return array
     */
    public function build(): array
    {
        return $this->params;
    }
}
