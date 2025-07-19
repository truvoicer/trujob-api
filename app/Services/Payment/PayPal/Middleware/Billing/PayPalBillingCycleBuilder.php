<?php

namespace App\Services\Payment\PayPal\Middleware\Billing;

use InvalidArgumentException;

/**
 * Class PayPalBillingCycleBuilder
 *
 * A fluent builder for constructing individual PayPal billing cycle data.
 * This is intended to be used within the PayPalBillingPlanBuilder.
 */
class PayPalBillingCycleBuilder
{
    /**
     * @var array The billing cycle data being built.
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
     * Static factory method to start building a new billing cycle.
     *
     * @return static
     */
    public static function build(): static
    {
        return new static();
    }

    /**
     * Sets the frequency of the billing cycle.
     *
     * @param string $intervalUnit The unit of the interval (e.g., 'DAY', 'WEEK', 'MONTH', 'YEAR').
     * @param int $intervalCount The number of intervals.
     * @return $this
     * @throws InvalidArgumentException If interval unit is invalid or count is not positive.
     */
    public function setFrequency(string $intervalUnit, int $intervalCount): self
    {
        $validUnits = ['DAY', 'WEEK', 'MONTH', 'YEAR'];
        $intervalUnit = strtoupper($intervalUnit);
        if (!in_array($intervalUnit, $validUnits)) {
            throw new InvalidArgumentException("Invalid interval unit '{$intervalUnit}'. Must be one of: " . implode(', ', $validUnits));
        }
        if ($intervalCount <= 0) {
            throw new InvalidArgumentException("Interval count must be a positive integer.");
        }

        $this->data['frequency'] = [
            'interval_unit' => $intervalUnit,
            'interval_count' => $intervalCount,
        ];
        return $this;
    }

    /**
     * Sets the tenure type of the billing cycle.
     *
     * @param string $tenureType The tenure type (e.g., 'REGULAR', 'TRIAL').
     * @return $this
     * @throws InvalidArgumentException If tenure type is invalid.
     */
    public function setTenureType(string $tenureType): self
    {
        $validTenureTypes = ['REGULAR', 'TRIAL'];
        $tenureType = strtoupper($tenureType);
        if (!in_array($tenureType, $validTenureTypes)) {
            throw new InvalidArgumentException("Invalid tenure type '{$tenureType}'. Must be one of: " . implode(', ', $validTenureTypes));
        }
        $this->data['tenure_type'] = $tenureType;
        return $this;
    }

    /**
     * Sets the sequence of the billing cycle in the plan.
     *
     * @param int $sequence The sequence number.
     * @return $this
     * @throws InvalidArgumentException If sequence is not positive.
     */
    public function setSequence(int $sequence): self
    {
        if ($sequence <= 0) {
            throw new InvalidArgumentException("Sequence must be a positive integer.");
        }
        $this->data['sequence'] = $sequence;
        return $this;
    }

    /**
     * Sets the total number of cycles for this billing cycle.
     *
     * @param int $totalCycles The total number of cycles.
     * @return $this
     * @throws InvalidArgumentException If total cycles is not positive.
     */
    public function setTotalCycles(int $totalCycles): self
    {
        if ($totalCycles <= 0) {
            throw new InvalidArgumentException("Total cycles must be a positive integer.");
        }
        $this->data['total_cycles'] = $totalCycles;
        return $this;
    }

    /**
     * Sets the pricing scheme for the billing cycle.
     *
     * @param string $currencyCode The currency code (e.g., 'USD').
     * @param string $value The value of the fixed price.
     * @param array $setupFee Optional setup fee details.
     * @return $this
     * @throws InvalidArgumentException If currency code or value is invalid.
     */
    public function setPricingScheme(string $currencyCode, string $value, array $setupFee = []): self
    {
        if (empty($currencyCode) || empty($value)) {
            throw new InvalidArgumentException("Currency code and value are required for pricing scheme.");
        }

        $this->data['pricing_scheme'] = [
            'fixed_price' => [
                'currency_code' => $currencyCode,
                'value' => $value,
            ],
        ];

        if (!empty($setupFee)) {
            // Validate setupFee structure if needed
            if (!isset($setupFee['value']) || !isset($setupFee['currency_code'])) {
                throw new InvalidArgumentException("Setup fee must contain 'value' and 'currency_code'.");
            }
            $this->data['pricing_scheme']['fixed_price']['setup_fee'] = $setupFee;
        }

        return $this;
    }

    /**
     * Returns the built billing cycle data array.
     *
     * @return array
     * @throws InvalidArgumentException If required fields are missing.
     */
    public function get(): array
    {
        if (!isset($this->data['frequency'])) {
            throw new InvalidArgumentException("Billing cycle frequency is required.");
        }
        if (!isset($this->data['tenure_type'])) {
            throw new InvalidArgumentException("Billing cycle tenure type is required.");
        }
        if (!isset($this->data['sequence'])) {
            throw new InvalidArgumentException("Billing cycle sequence is required.");
        }
        if (!isset($this->data['total_cycles'])) {
            throw new InvalidArgumentException("Billing cycle total cycles is required.");
        }
        if (!isset($this->data['pricing_scheme'])) {
            throw new InvalidArgumentException("Billing cycle pricing scheme is required.");
        }

        return $this->data;
    }
}
