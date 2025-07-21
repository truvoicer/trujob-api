<?php

namespace Tests\Unit\Services\Payment\PayPal\Middleware\Billing;

use App\Services\Payment\PayPal\Middleware\Billing\PayPalBillingCycleBuilder;
use InvalidArgumentException;
use Tests\TestCase;

class PayPalBillingCycleBuilderTest extends TestCase
{
    /**
     * @var PayPalBillingCycleBuilder
     */
    protected PayPalBillingCycleBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = PayPalBillingCycleBuilder::build();
    }

    public function testSetFrequencySuccess(): void
    {
        $this->builder->setFrequency('MONTH', 1);
        $data = $this->builder->get();
        $this->assertArrayHasKey('frequency', $data);
        $this->assertEquals([
            'interval_unit' => 'MONTH',
            'interval_count' => 1,
        ], $data['frequency']);
    }

    public function testSetFrequencyInvalidIntervalUnit(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid interval unit 'INVALID'. Must be one of: DAY, WEEK, MONTH, YEAR");
        $this->builder->setFrequency('INVALID', 1);
    }

    public function testSetFrequencyInvalidIntervalCount(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Interval count must be a positive integer.");
        $this->builder->setFrequency('MONTH', 0);
    }

    public function testSetTenureTypeSuccess(): void
    {
        $this->builder->setTenureType('REGULAR');
        $data = $this->builder->get();
        $this->assertArrayHasKey('tenure_type', $data);
        $this->assertEquals('REGULAR', $data['tenure_type']);
    }

    public function testSetTenureTypeInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid tenure type 'INVALID'. Must be one of: REGULAR, TRIAL");
        $this->builder->setTenureType('INVALID');
    }

    public function testSetSequenceSuccess(): void
    {
        $this->builder->setSequence(1);
        $data = $this->builder->get();
        $this->assertArrayHasKey('sequence', $data);
        $this->assertEquals(1, $data['sequence']);
    }

    public function testSetSequenceInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Sequence must be a positive integer.");
        $this->builder->setSequence(0);
    }

    public function testSetTotalCyclesSuccess(): void
    {
        $this->builder->setTotalCycles(12);
        $data = $this->builder->get();
        $this->assertArrayHasKey('total_cycles', $data);
        $this->assertEquals(12, $data['total_cycles']);
    }

    public function testSetTotalCyclesInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Total cycles must be a positive integer.");
        $this->builder->setTotalCycles(0);
    }

    public function testSetPricingSchemeSuccess(): void
    {
        $this->builder->setPricingScheme('USD', '100.00');
        $data = $this->builder->get();
        $this->assertArrayHasKey('pricing_scheme', $data);
        $this->assertEquals([
            'fixed_price' => [
                'currency_code' => 'USD',
                'value' => '100.00',
            ],
        ], $data['pricing_scheme']);
    }

    public function testSetPricingSchemeWithSetupFeeSuccess(): void
    {
        $this->builder->setPricingScheme('USD', '100.00', ['currency_code' => 'USD', 'value' => '10.00']);
        $data = $this->builder->get();
        $this->assertArrayHasKey('pricing_scheme', $data);
        $this->assertEquals([
            'fixed_price' => [
                'currency_code' => 'USD',
                'value' => '100.00',
                'setup_fee' => ['currency_code' => 'USD', 'value' => '10.00']
            ],
        ], $data['pricing_scheme']);
    }

    public function testSetPricingSchemeMissingCurrencyCode(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Currency code and value are required for pricing scheme.");
        $this->builder->setPricingScheme('', '100.00');
    }

    public function testSetPricingSchemeMissingValue(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Currency code and value are required for pricing scheme.");
        $this->builder->setPricingScheme('USD', '');
    }

    public function testSetPricingSchemeInvalidSetupFee(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Setup fee must contain 'value' and 'currency_code'.");
        $this->builder->setPricingScheme('USD', '100.00', ['invalid' => 'data']);
    }

    public function testGetSuccess(): void
    {
        $this->builder->setFrequency('MONTH', 1)
            ->setTenureType('REGULAR')
            ->setSequence(1)
            ->setTotalCycles(12)
            ->setPricingScheme('USD', '100.00');

        $data = $this->builder->get();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('frequency', $data);
        $this->assertArrayHasKey('tenure_type', $data);
        $this->assertArrayHasKey('sequence', $data);
        $this->assertArrayHasKey('total_cycles', $data);
        $this->assertArrayHasKey('pricing_scheme', $data);
    }

    public function testGetMissingFrequency(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Billing cycle frequency is required.");
        $this->builder->setTenureType('REGULAR')
            ->setSequence(1)
            ->setTotalCycles(12)
            ->setPricingScheme('USD', '100.00')
            ->get();
    }

    public function testGetMissingTenureType(): void
    {
        $this->builder->setFrequency('MONTH', 1);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Billing cycle tenure type is required.");

        $this->builder->setSequence(1)
            ->setTotalCycles(12)
            ->setPricingScheme('USD', '100.00')
            ->get();
    }

    public function testGetMissingSequence(): void
    {
        $this->builder->setFrequency('MONTH', 1)
        ->setTenureType('REGULAR');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Billing cycle sequence is required.");
        $this->builder->setTotalCycles(12)
            ->setPricingScheme('USD', '100.00')
            ->get();
    }

    public function testGetMissingTotalCycles(): void
    {
        $this->builder->setFrequency('MONTH', 1)
        ->setTenureType('REGULAR')
        ->setSequence(1);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Billing cycle total cycles is required.");
        $this->builder->setPricingScheme('USD', '100.00')
            ->get();
    }

    public function testGetMissingPricingScheme(): void
    {
        $this->builder->setFrequency('MONTH', 1)
        ->setTenureType('REGULAR')
        ->setSequence(1)
        ->setTotalCycles(12);
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Billing cycle pricing scheme is required.");
        $this->builder->get();
    }
}