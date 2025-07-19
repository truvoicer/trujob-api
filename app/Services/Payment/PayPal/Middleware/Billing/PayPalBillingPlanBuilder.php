<?php

namespace App\Services\Payment\PayPal\Middleware\Billing;

use InvalidArgumentException;

/**
 * Class PayPalBillingPlanBuilder
 *
 * A fluent builder for constructing PayPal billing plan data arrays.
 * This builder allows adding multiple billing cycles using PayPalBillingCycleBuilder.
 */
class PayPalBillingPlanBuilder
{
    /**
     * @var array The billing plan data being built.
     */
    protected array $data = [];

    /**
     * @var array An array to hold billing cycle data.
     */
    protected array $billingCycles = [];

    /**
     * Private constructor to enforce static `build()` method usage.
     */
    private function __construct()
    {
        $this->data = [];
        $this->billingCycles = [];
    }

    /**
     * Static factory method to start building a new billing plan.
     *
     * @return static
     */
    public static function build(): static
    {
        return new static();
    }

    /**
     * Sets the product ID for the billing plan.
     *
     * @param string $productId The ID of the product this plan is for.
     * @return $this
     */
    public function setProductId(string $productId): self
    {
        $this->data['product_id'] = $productId;
        return $this;
    }

    /**
     * Sets the name of the billing plan.
     *
     * @param string $name The plan name.
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->data['name'] = $name;
        return $this;
    }

    /**
     * Sets the description of the billing plan.
     *
     * @param string $description The plan description.
     * @return $this
     */
    public function setDescription(string $description): self
    {
        $this->data['description'] = $description;
        return $this;
    }

    /**
     * Sets the type of the billing plan.
     *
     * @param string $type The plan type (e.g., 'FIXED', 'INFINITE').
     * @return $this
     * @throws InvalidArgumentException If the type is not valid.
     */
    public function setType(string $type): self
    {
        $validTypes = ['FIXED', 'INFINITE'];
        $type = strtoupper($type);
        if (!in_array($type, $validTypes)) {
            throw new InvalidArgumentException("Invalid plan type '{$type}'. Must be one of: " . implode(', ', $validTypes));
        }
        $this->data['type'] = $type;
        return $this;
    }

    /**
     * Sets the status of the billing plan.
     *
     * @param string $status The plan status (e.g., 'CREATED', 'ACTIVE', 'INACTIVE').
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->data['status'] = $status;
        return $this;
    }

    public function setSetupFee(string $value, string $currencyCode): self
    {
        if (empty($this->data['payment_preferences'])) {
            $this->data['payment_preferences'] = [];
        }
        $this->data['payment_preferences']['setup_fee'] = [
            'value' => $value,
            'currency_code' => $currencyCode,
        ];
        return $this;
    }
    /**
     * Sets the payment preference for the plan.
     *
     * @param bool $autoBillOutstanding True to auto-bill outstanding, false otherwise.
     * @param string $setupFeeFailureAction Action on setup fee failure (e.g., 'CONTINUE', 'CANCEL').
     * @param int $paymentFailureThreshold The number of payment failures before the subscription is suspended.
     * @return $this
     * @throws InvalidArgumentException If setup fee failure action is invalid or threshold is not positive.
     */
    public function setPaymentPreferences(
        bool $autoBillOutstanding,
        string $setupFeeFailureAction,
        int $paymentFailureThreshold
    ): self {
        $validActions = ['CONTINUE', 'CANCEL'];
        $setupFeeFailureAction = strtoupper($setupFeeFailureAction);
        if (!in_array($setupFeeFailureAction, $validActions)) {
            throw new InvalidArgumentException("Invalid setup fee failure action '{$setupFeeFailureAction}'. Must be one of: " . implode(', ', $validActions));
        }
        if ($paymentFailureThreshold <= 0) {
            throw new InvalidArgumentException("Payment failure threshold must be a positive integer.");
        }

        $this->data['payment_preferences'] = [
            'auto_bill_outstanding' => $autoBillOutstanding,
            'setup_fee_failure_action' => $setupFeeFailureAction,
            'payment_failure_threshold' => $paymentFailureThreshold,
        ];
        return $this;
    }

    /**
     * Adds a billing cycle to the plan using a PayPalBillingCycleBuilder instance.
     *
     * @param PayPalBillingCycleBuilder $billingCycleBuilder The builder for a single billing cycle.
     * @return $this
     */
    public function addBillingCycle(PayPalBillingCycleBuilder $billingCycleBuilder): self
    {
        $this->billingCycles[] = $billingCycleBuilder->get();
        return $this;
    }

    /**
     * Returns the built billing plan data array.
     *
     * @return array
     * @throws InvalidArgumentException If required fields are missing or billing cycles are not set.
     */
    public function get(): array
    {
        if (!isset($this->data['product_id'])) {
            throw new InvalidArgumentException("Product ID is required for the billing plan.");
        }
        if (!isset($this->data['name'])) {
            throw new InvalidArgumentException("Billing plan name is required.");
        }
        if (!isset($this->data['type'])) {
            throw new InvalidArgumentException("Billing plan type is required.");
        }
        if (empty($this->billingCycles)) {
            throw new InvalidArgumentException("At least one billing cycle is required for the billing plan.");
        }

        $this->data['billing_cycles'] = $this->billingCycles;

        return $this->data;
    }
}
