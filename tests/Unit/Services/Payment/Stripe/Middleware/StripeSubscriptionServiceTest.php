<?php

namespace Tests\Unit\Services\Payment\Stripe\Middleware;

use App\Services\Payment\Stripe\Middleware\StripeSubscriptionService;
use Illuminate\Support\Facades\Log;
use Stripe\Subscription;
use Tests\TestCase;

class StripeSubscriptionServiceTest extends TestCase
{
    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|StripeSubscriptionService
     */
    private $stripeSubscriptionService;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|Subscription
     */
    private $mockSubscription;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the Subscription class
        $this->mockSubscription = $this->createMock(Subscription::class);

        // Create a partial mock of the StripeSubscriptionService, mocking only callStripeApi
        $this->stripeSubscriptionService = $this->getMockBuilder(StripeSubscriptionService::class)
            ->onlyMethods(['callStripeApi'])
            ->getMock();
    }

    public function testCreateSubscription(): void
    {
        $customerId = 'cus_123';
        $items = [['price' => 'price_123', 'quantity' => 1]];
        $options = ['trial_end' => time()];

        $this->stripeSubscriptionService->method('callStripeApi')->willReturnCallback(function ($callback) {
            return $callback();
        });

        Subscription::shouldReceive('create')
            ->once()
            ->with(array_merge([
                'customer' => $customerId,
                'items' => $items,
            ], $options))
            ->andReturn($this->mockSubscription);

        $result = $this->stripeSubscriptionService->createSubscription($customerId, $items, $options);

        $this->assertInstanceOf(Subscription::class, $result);
    }

    public function testRetrieveSubscription(): void
    {
        $subscriptionId = 'sub_123';
        $options = ['expand' => ['latest_invoice']];

        $this->stripeSubscriptionService->method('callStripeApi')->willReturnCallback(function ($callback) {
            return $callback();
        });

        Subscription::shouldReceive('retrieve')
            ->once()
            ->with($subscriptionId, $options)
            ->andReturn($this->mockSubscription);

        $result = $this->stripeSubscriptionService->retrieveSubscription($subscriptionId, $options);

        $this->assertInstanceOf(Subscription::class, $result);
    }

    public function testUpdateSubscription(): void
    {
        $subscriptionId = 'sub_123';
        $updates = ['cancel_at_period_end' => true];

        $this->stripeSubscriptionService->method('callStripeApi')->willReturnCallback(function ($callback) {
            return $callback();
        });

        Subscription::shouldReceive('retrieve')
            ->once()
            ->with($subscriptionId)
            ->andReturn($this->mockSubscription);

        $this->mockSubscription->expects($this->once())
            ->method('update')
            ->with($updates)
            ->willReturn($this->mockSubscription);

        $result = $this->stripeSubscriptionService->updateSubscription($subscriptionId, $updates);

        $this->assertInstanceOf(Subscription::class, $result);
    }

    public function testCancelSubscription(): void
    {
        $subscriptionId = 'sub_123';
        $options = ['at_period_end' => true];

        $this->stripeSubscriptionService->method('callStripeApi')->willReturnCallback(function ($callback) {
            return $callback();
        });

        Subscription::shouldReceive('retrieve')
            ->once()
            ->with($subscriptionId)
            ->andReturn($this->mockSubscription);

        $this->mockSubscription->expects($this->once())
            ->method('cancel')
            ->with($options)
            ->willReturn($this->mockSubscription);

        $result = $this->stripeSubscriptionService->cancelSubscription($subscriptionId, $options);

        $this->assertInstanceOf(Subscription::class, $result);
    }

    public function testDeleteSubscription(): void
    {
        $subscriptionId = 'sub_123';

        $this->stripeSubscriptionService->method('callStripeApi')->willReturnCallback(function ($callback) {
            return $callback();
        });

        Subscription::shouldReceive('retrieve')
            ->once()
            ->with($subscriptionId)
            ->andReturn($this->mockSubscription);

        $this->mockSubscription->expects($this->once())
            ->method('delete')
            ->willReturn($this->mockSubscription);

        $result = $this->stripeSubscriptionService->deleteSubscription($subscriptionId);

        $this->assertInstanceOf(Subscription::class, $result);
    }
}