<?php

namespace Tests\Unit\Services\Payment\Stripe\Middleware\Checkout;

use App\Services\Payment\Stripe\Middleware\Checkout\StripeCheckoutService;
use App\Services\Payment\Stripe\Middleware\Checkout\StripeCheckoutSessionBuilder;
use Illuminate\Support\Facades\Log;
use Stripe\Checkout\Session;
use Stripe\StripeClient;
use Tests\TestCase;

class StripeCheckoutServiceTest extends TestCase
{
    private StripeCheckoutService $stripeCheckoutService;
    private \Mockery\MockInterface $stripeClientMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the StripeClient
        $this->stripeClientMock = \Mockery::mock(StripeClient::class);

        // Instantiate the service with the mock
        $this->stripeCheckoutService = new StripeCheckoutService();
        $this->stripeCheckoutService->setStripeClient($this->stripeClientMock);

        Log::shouldReceive('info')->zeroOrMoreTimes();
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testCreateOneTimePaymentSession(): void
    {
        // Arrange
        $builder = new StripeCheckoutSessionBuilder();
        $builder->setSuccessUrl('https://example.com/success');
        $builder->setCancelUrl('https://example.com/cancel');
        $builder->addLineItem('price_123', 1);

        $mockSession = new Session('sess_123');
        $checkoutSessions = \Mockery::mock();
        $checkoutSessions->shouldReceive('create')->once()->andReturn($mockSession);
        $this->stripeClientMock->checkout = (object) ['sessions' => $checkoutSessions];

        // Act
        $session = $this->stripeCheckoutService->createOneTimePaymentSession($builder);

        // Assert
        $this->assertInstanceOf(Session::class, $session);
        $this->assertEquals('sess_123', $session->id);
    }

    public function testCreateSubscriptionSession(): void
    {
        // Arrange
        $builder = new StripeCheckoutSessionBuilder();
        $builder->setSuccessUrl('https://example.com/success');
        $builder->setCancelUrl('https://example.com/cancel');
        $builder->addLineItem('price_456', 1);
        $builder->setMode('subscription');

        $mockSession = new Session('sess_456');
        $checkoutSessions = \Mockery::mock();
        $checkoutSessions->shouldReceive('create')->once()->andReturn($mockSession);
        $this->stripeClientMock->checkout = (object) ['sessions' => $checkoutSessions];

        // Act
        $session = $this->stripeCheckoutService->createSubscriptionSession($builder);

        // Assert
        $this->assertInstanceOf(Session::class, $session);
        $this->assertEquals('sess_456', $session->id);
    }

    public function testRetrieveSession(): void
    {
        // Arrange
        $sessionId = 'sess_789';
        $mockSession = new Session($sessionId);
        $checkoutSessions = \Mockery::mock();
        $checkoutSessions->shouldReceive('retrieve')->once()->with($sessionId, [])->andReturn($mockSession);
        $this->stripeClientMock->checkout = (object) ['sessions' => $checkoutSessions];


        // Act
        $session = $this->stripeCheckoutService->retrieveSession($sessionId);

        // Assert
        $this->assertInstanceOf(Session::class, $session);
        $this->assertEquals($sessionId, $session->id);
    }
}