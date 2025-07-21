<?php

namespace Tests\Unit\Services\Payment\PayPal\Middleware\Billing;

use App\Services\Payment\PayPal\Middleware\Billing\PayPalBillingPlanService;
use App\Services\Payment\PayPal\Middleware\Billing\PayPalBillingPlanBuilder;
use Exception;
use Illuminate\Foundation\Testing\TestCase;
use Tests\CreatesApplication;

class PayPalBillingPlanServiceTest extends TestCase
{
    use CreatesApplication;

    private $payPalBillingPlanService;

    protected function setUp(): void
    {
        parent::setUp();
        // Mock the PayPalBillingPlanService, but allow method calls so we can test exceptions.
        $this->payPalBillingPlanService = $this->getMockBuilder(PayPalBillingPlanService::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['makeRequest']) // Only mock the makeRequest method
            ->getMock();
    }

    public function testCreatePlanSuccess(): void
    {
        $builder = $this->createMock(PayPalBillingPlanBuilder::class);
        $planData = ['name' => 'Test Plan', 'description' => 'Test Description', 'type' => 'FIXED'];
        $builder->method('get')->willReturn($planData);

        $expectedResponse = ['id' => 'P-TESTPLAN123'];
        $this->payPalBillingPlanService->method('makeRequest')->willReturn($expectedResponse);

        $actualResponse = $this->payPalBillingPlanService->createPlan($builder);

        $this->assertEquals($expectedResponse, $actualResponse);
    }

    public function testCreatePlanFailure(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Billing plan creation failed: No plan ID returned.');

        $builder = $this->createMock(PayPalBillingPlanBuilder::class);
        $planData = ['name' => 'Test Plan', 'description' => 'Test Description', 'type' => 'FIXED'];
        $builder->method('get')->willReturn($planData);

        $this->payPalBillingPlanService->method('makeRequest')->willReturn([]); // Simulate failure
        $this->payPalBillingPlanService->createPlan($builder);
    }

    public function testListPlansSuccess(): void
    {
        $expectedResponse = ['plans' => [['id' => 'P-TESTPLAN123'], ['id' => 'P-TESTPLAN456']]];
        $this->payPalBillingPlanService->method('makeRequest')->willReturn($expectedResponse);

        $actualResponse = $this->payPalBillingPlanService->listPlans(10, 1, 'PRODUCT-ID', 'ACTIVE');

        $this->assertEquals($expectedResponse, $actualResponse);
    }

    public function testListPlansFailure(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Failed to list PayPal billing plans: Test error');

        $this->payPalBillingPlanService->method('makeRequest')->willThrowException(new Exception('Test error'));
        $this->payPalBillingPlanService->listPlans();
    }

    public function testShowPlanSuccess(): void
    {
        $planId = 'P-TESTPLAN123';
        $expectedResponse = ['id' => $planId, 'name' => 'Test Plan'];
        $this->payPalBillingPlanService->method('makeRequest')->willReturn($expectedResponse);

        $actualResponse = $this->payPalBillingPlanService->showPlan($planId);

        $this->assertEquals($expectedResponse, $actualResponse);
    }

    public function testShowPlanFailure(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed to retrieve PayPal billing plan 'P-TESTPLAN123': Test error");

        $planId = 'P-TESTPLAN123';
        $this->payPalBillingPlanService->method('makeRequest')->willThrowException(new Exception('Test error'));
        $this->payPalBillingPlanService->showPlan($planId);
    }

    public function testUpdatePlanSuccess(): void
    {
        $planId = 'P-TESTPLAN123';
        $patchData = [['op' => 'replace', 'path' => '/description', 'value' => 'New description']];
        $expectedResponse = []; // Expecting a 204 No Content, which translates to an empty array

        $this->payPalBillingPlanService->method('makeRequest')->willReturn($expectedResponse);

        $actualResponse = $this->payPalBillingPlanService->updatePlan($planId, $patchData);

        $this->assertEquals($expectedResponse, $actualResponse);
    }

    public function testUpdatePlanFailure(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed to update PayPal billing plan 'P-TESTPLAN123': Test error");

        $planId = 'P-TESTPLAN123';
        $patchData = [['op' => 'replace', 'path' => '/description', 'value' => 'New description']];
        $this->payPalBillingPlanService->method('makeRequest')->willThrowException(new Exception('Test error'));
        $this->payPalBillingPlanService->updatePlan($planId, $patchData);
    }

    public function testActivatePlanSuccess(): void
    {
        $planId = 'P-TESTPLAN123';
        $this->payPalBillingPlanService->method('makeRequest')->willReturn(null); // Simulate success

        $result = $this->payPalBillingPlanService->activatePlan($planId);

        $this->assertTrue($result);
    }

    public function testActivatePlanFailure(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed to activate PayPal billing plan 'P-TESTPLAN123': Test error");

        $planId = 'P-TESTPLAN123';
        $this->payPalBillingPlanService->method('makeRequest')->willThrowException(new Exception('Test error'));
        $this->payPalBillingPlanService->activatePlan($planId);
    }

    public function testDeactivatePlanSuccess(): void
    {
        $planId = 'P-TESTPLAN123';
        $this->payPalBillingPlanService->method('makeRequest')->willReturn(null); // Simulate success

        $result = $this->payPalBillingPlanService->deactivatePlan($planId);

        $this->assertTrue($result);
    }

    public function testDeactivatePlanFailure(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed to deactivate PayPal billing plan 'P-TESTPLAN123': Test error");

        $planId = 'P-TESTPLAN123';
        $this->payPalBillingPlanService->method('makeRequest')->willThrowException(new Exception('Test error'));
        $this->payPalBillingPlanService->deactivatePlan($planId);
    }

    public function testDeletePlanThrowsException(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Direct deletion of billing plans is not supported by PayPal Subscriptions API v1. Consider deactivating the plan instead.");

        $planId = 'P-TESTPLAN123';
        $this->payPalBillingPlanService->deletePlan($planId);
    }
}