<?php

namespace Tests\Unit\Services\Payment\PayPal\Middleware\Billing;

use App\Services\Payment\PayPal\Middleware\Billing\PayPalBillingPlanBuilder;
use App\Services\Payment\PayPal\Middleware\Billing\PayPalBillingCycleBuilder;
use InvalidArgumentException;
use Tests\TestCase;

class PayPalBillingPlanBuilderTest extends TestCase
{
    /**
     * @var PayPalBillingPlanBuilder
     */
    private PayPalBillingPlanBuilder $builder;

    protected function setUp(): void
    {
        parent::setUp();
        $this->builder = PayPalBillingPlanBuilder::build();
    }

    public function testSetProductId(): void
    {
        $this->assertInstanceOf(PayPalBillingPlanBuilder::class, $this->builder->setProductId('test_product_id'));
        $data = $this->builder->get();
        $this->assertArrayHasKey('product_id', $data);
        $this->assertEquals('test_product_id', $data['product_id']);
    }

    public function testSetName(): void
    {
        $this->assertInstanceOf(PayPalBillingPlanBuilder::class, $this->builder->setName('Test Plan'));
        $data = $this->builder->get();
        $this->assertArrayHasKey('name', $data);
        $this->assertEquals('Test Plan', $data['name']);
    }

    public function testSetDescription(): void
    {
        $this->assertInstanceOf(PayPalBillingPlanBuilder::class, $this->builder->setDescription('Test Description'));
        $data = $this->builder->get();
        $this->assertArrayHasKey('description', $data);
        $this->assertEquals('Test Description', $data['description']);
    }

    public function testSetType(): void
    {
        $this->assertInstanceOf(PayPalBillingPlanBuilder::class, $this->builder->setType('FIXED'));
        $data = $this->builder->get();
        $this->assertArrayHasKey('type', $data);
        $this->assertEquals('FIXED', $data['type']);
    }

    public function testSetTypeInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid plan type 'INVALID'. Must be one of: FIXED, INFINITE");

        $this->builder->setType('INVALID');
    }

    public function testSetStatus(): void
    {
        $this->assertInstanceOf(PayPalBillingPlanBuilder::class, $this->builder->setStatus('ACTIVE'));
        $data = $this->builder->get();
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('ACTIVE', $data['status']);
    }

    public function testSetSetupFee(): void
    {
        $this->assertInstanceOf(PayPalBillingPlanBuilder::class, $this->builder->setSetupFee('10.00', 'USD'));
        $data = $this->builder->get();
        $this->assertArrayHasKey('payment_preferences', $data);
        $this->assertArrayHasKey('setup_fee', $data['payment_preferences']);
        $this->assertEquals([
            'value' => '10.00',
            'currency_code' => 'USD',
        ], $data['payment_preferences']['setup_fee']);
    }

    public function testSetPaymentPreferences(): void
    {
        $this->assertInstanceOf(PayPalBillingPlanBuilder::class, $this->builder->setPaymentPreferences(true, 'CONTINUE', 3));
        $data = $this->builder->get();
        $this->assertArrayHasKey('payment_preferences', $data);
        $this->assertEquals(true, $data['payment_preferences']['auto_bill_outstanding']);
        $this->assertEquals('CONTINUE', $data['payment_preferences']['setup_fee_failure_action']);
        $this->assertEquals(3, $data['payment_preferences']['payment_failure_threshold']);
    }

    public function testSetPaymentPreferencesInvalidAction(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid setup fee failure action 'INVALID'. Must be one of: CONTINUE, CANCEL");

        $this->builder->setPaymentPreferences(true, 'INVALID', 3);
    }

    public function testSetPaymentPreferencesInvalidThreshold(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Payment failure threshold must be a positive integer.");

        $this->builder->setPaymentPreferences(true, 'CONTINUE', 0);
    }

    public function testAddBillingCycle(): void
    {
        $cycleBuilder = $this->createMock(PayPalBillingCycleBuilder::class);
        $cycleBuilder->method('get')->willReturn(['cycle_data' => 'test']);

        $this->assertInstanceOf(PayPalBillingPlanBuilder::class, $this->builder->addBillingCycle($cycleBuilder));
        $data = $this->builder->get();
        $this->assertArrayHasKey('billing_cycles', $data);
        $this->assertCount(1, $data['billing_cycles']);
        $this->assertEquals(['cycle_data' => 'test'], $data['billing_cycles'][0]);
    }

    public function testGet(): void
    {
        $cycleBuilder = $this->createMock(PayPalBillingCycleBuilder::class);
        $cycleBuilder->method('get')->willReturn(['cycle_data' => 'test']);

        $this->builder->setProductId('test_product_id')
            ->setName('Test Plan')
            ->setType('FIXED')
            ->addBillingCycle($cycleBuilder);

        $data = $this->builder->get();

        $this->assertIsArray($data);
        $this->assertArrayHasKey('product_id', $data);
        $this->assertArrayHasKey('name', $data);
        $this->assertArrayHasKey('type', $data);
        $this->assertArrayHasKey('billing_cycles', $data);
    }

    public function testGetMissingProductId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Product ID is required for the billing plan.");

        $cycleBuilder = $this->createMock(PayPalBillingCycleBuilder::class);
        $cycleBuilder->method('get')->willReturn(['cycle_data' => 'test']);

        $this->builder->setName('Test Plan')
            ->setType('FIXED')
            ->addBillingCycle($cycleBuilder)
            ->get();
    }

    public function testGetMissingName(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Billing plan name is required.");

        $cycleBuilder = $this->createMock(PayPalBillingCycleBuilder::class);
        $cycleBuilder->method('get')->willReturn(['cycle_data' => 'test']);

        $this->builder->setProductId('test_product_id')
            ->setType('FIXED')
            ->addBillingCycle($cycleBuilder)
            ->get();
    }

    public function testGetMissingType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Billing plan type is required.");

        $cycleBuilder = $this->createMock(PayPalBillingCycleBuilder::class);
        $cycleBuilder->method('get')->willReturn(['cycle_data' => 'test']);

        $this->builder->setProductId('test_product_id')
            ->setName('Test Plan')
            ->addBillingCycle($cycleBuilder)
            ->get();
    }

    public function testGetMissingBillingCycles(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("At least one billing cycle is required for the billing plan.");

        $this->builder->setProductId('test_product_id')
            ->setName('Test Plan')
            ->setType('FIXED')
            ->get();
    }
}