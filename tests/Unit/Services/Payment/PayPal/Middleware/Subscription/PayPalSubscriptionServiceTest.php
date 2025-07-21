<?php

namespace Tests\Unit\Services\Payment\PayPal\Middleware\Subscription;

use App\Services\Payment\PayPal\Middleware\Subscription\PayPalSubscriptionService;
use App\Services\Payment\PayPal\Middleware\Subscription\PayPalSubscriptionBuilder;
use App\Services\Payment\PayPal\Middleware\Subscription\PayPalSubscriptionResponseHandler;
use Tests\TestCase;
use Mockery;
use Mockery\MockInterface;
use Exception;

class PayPalSubscriptionServiceTest extends TestCase
{
    /**
     * @var MockInterface|PayPalSubscriptionService
     */
    private $payPalSubscriptionService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a partial mock so we can mock only the makeRequest method
        $this->payPalSubscriptionService = Mockery::mock(PayPalSubscriptionService::class)->makePartial();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCreateSubscriptionSuccess(): void
    {
        // Arrange
        $builder = Mockery::mock(PayPalSubscriptionBuilder::class);
        $builder->shouldReceive('get')->once()->andReturn(['plan_id' => 'test_plan_id']);
        $mockResponse = ['id' => 'test_subscription_id'];
        $responseHandler = new PayPalSubscriptionResponseHandler($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('makeRequest')
            ->once()
            ->with('POST', Mockery::any(), ['plan_id' => 'test_plan_id'])
            ->andReturn($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('handleResponse')
            ->once()
            ->with(Mockery::type(PayPalSubscriptionResponseHandler::class))
            ->andReturn($responseHandler);

        // Act
        $result = $this->payPalSubscriptionService->createSubscription($builder);

        // Assert
        $this->assertInstanceOf(PayPalSubscriptionResponseHandler::class, $result);
        $this->assertEquals('test_subscription_id', $result->toArray()['id']);
    }

    public function testCreateSubscriptionFailure(): void
    {
        // Arrange
        $builder = Mockery::mock(PayPalSubscriptionBuilder::class);
        $builder->shouldReceive('get')->once()->andThrow(new Exception('Builder error'));

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed to create PayPal subscription: Builder error");

        // Act
        $this->payPalSubscriptionService->createSubscription($builder);
    }

    public function testListSubscriptionsSuccess(): void
    {
        // Arrange
        $mockResponse = ['subscriptions' => []];
        $responseHandler = new PayPalSubscriptionResponseHandler($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('makeRequest')
            ->once()
            ->with('GET', Mockery::any())
            ->andReturn($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('handleResponse')
            ->once()
            ->with(Mockery::type(PayPalSubscriptionResponseHandler::class))
            ->andReturn($responseHandler);

        // Act
        $result = $this->payPalSubscriptionService->listSubscriptions(10, 1, 'test_plan_id', 'ACTIVE');

        // Assert
        $this->assertInstanceOf(PayPalSubscriptionResponseHandler::class, $result);
        $this->assertIsArray($result->toArray()['subscriptions']);
    }

    public function testListSubscriptionsFailure(): void
    {
        // Arrange
        $this->payPalSubscriptionService->shouldReceive('makeRequest')
            ->once()
            ->with('GET', Mockery::any())
            ->andThrow(new Exception('Request error'));

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed to list PayPal subscriptions: Request error");

        // Act
        $this->payPalSubscriptionService->listSubscriptions();
    }

    public function testShowSubscriptionSuccess(): void
    {
        // Arrange
        $subscriptionId = 'test_subscription_id';
        $mockResponse = ['id' => $subscriptionId];
        $responseHandler = new PayPalSubscriptionResponseHandler($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('makeRequest')
            ->once()
            ->with('GET', Mockery::any())
            ->andReturn($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('handleResponse')
            ->once()
            ->with(Mockery::type(PayPalSubscriptionResponseHandler::class))
            ->andReturn($responseHandler);

        // Act
        $result = $this->payPalSubscriptionService->showSubscription($subscriptionId);

        // Assert
        $this->assertInstanceOf(PayPalSubscriptionResponseHandler::class, $result);
        $this->assertEquals($subscriptionId, $result->toArray()['id']);
    }

    public function testShowSubscriptionFailure(): void
    {
        // Arrange
        $subscriptionId = 'test_subscription_id';
        $this->payPalSubscriptionService->shouldReceive('makeRequest')
            ->once()
            ->with('GET', Mockery::any())
            ->andThrow(new Exception('Request error'));

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed to retrieve PayPal subscription '{$subscriptionId}': Request error");

        // Act
        $this->payPalSubscriptionService->showSubscription($subscriptionId);
    }

    public function testUpdateSubscriptionSuccess(): void
    {
        // Arrange
        $subscriptionId = 'test_subscription_id';
        $patchData = [['op' => 'replace', 'path' => '/shipping_amount/value', 'value' => '15.00']];
        $mockResponse = []; // PayPal PATCH typically returns a 204 No Content.
        $responseHandler = new PayPalSubscriptionResponseHandler($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('makeRequest')
            ->once()
            ->with('PATCH', Mockery::any(), $patchData, ['Content-Type: application/json-patch+json'])
            ->andReturn($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('handleResponse')
            ->once()
            ->with(Mockery::type(PayPalSubscriptionResponseHandler::class))
            ->andReturn($responseHandler);

        // Act
        $result = $this->payPalSubscriptionService->updateSubscription($subscriptionId, $patchData);

        // Assert
        $this->assertInstanceOf(PayPalSubscriptionResponseHandler::class, $result);
        $this->assertEmpty($result->toArray()); // Because of 204 No Content
    }

    public function testUpdateSubscriptionFailure(): void
    {
        // Arrange
        $subscriptionId = 'test_subscription_id';
        $patchData = [['op' => 'replace', 'path' => '/shipping_amount/value', 'value' => '15.00']];
        $this->payPalSubscriptionService->shouldReceive('makeRequest')
            ->once()
            ->with('PATCH', Mockery::any(), $patchData, ['Content-Type: application/json-patch+json'])
            ->andThrow(new Exception('Request error'));

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed to update PayPal subscription '{$subscriptionId}': Request error");

        // Act
        $this->payPalSubscriptionService->updateSubscription($subscriptionId, $patchData);
    }

    public function testReviseSubscriptionSuccess(): void
    {
        // Arrange
        $subscriptionId = 'test_subscription_id';
        $planId = 'new_plan_id';
        $quantity = 2;
        $shippingAmount = ['currency_code' => 'USD', 'value' => '10.00'];
        $reviseData = [
            'plan_id' => $planId,
            'quantity' => $quantity,
            'shipping_amount' => $shippingAmount,
        ];

        $mockResponse = ['id' => $subscriptionId, 'plan_id' => $planId];
        $responseHandler = new PayPalSubscriptionResponseHandler($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('makeRequest')
            ->once()
            ->with('POST', Mockery::any(), $reviseData)
            ->andReturn($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('handleResponse')
            ->once()
            ->with(Mockery::type(PayPalSubscriptionResponseHandler::class))
            ->andReturn($responseHandler);

        // Act
        $result = $this->payPalSubscriptionService->reviseSubscription($subscriptionId, $planId, $quantity, $shippingAmount);

        // Assert
        $this->assertInstanceOf(PayPalSubscriptionResponseHandler::class, $result);
        $this->assertEquals($planId, $result->toArray()['plan_id']);
    }

    public function testReviseSubscriptionFailure(): void
    {
        // Arrange
        $subscriptionId = 'test_subscription_id';
        $planId = 'new_plan_id';
        $this->payPalSubscriptionService->shouldReceive('makeRequest')
            ->once()
            ->with('POST', Mockery::any(), Mockery::any())
            ->andThrow(new Exception('Request error'));

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed to revise PayPal subscription '{$subscriptionId}': Request error");

        // Act
        $this->payPalSubscriptionService->reviseSubscription($subscriptionId, $planId);
    }

    public function testSuspendSubscriptionSuccess(): void
    {
        // Arrange
        $subscriptionId = 'test_subscription_id';
        $reason = 'Test suspension';
        $mockResponse = ['id' => $subscriptionId, 'status' => 'SUSPENDED'];
        $responseHandler = new PayPalSubscriptionResponseHandler($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('makeRequest')
            ->once()
            ->with('POST', Mockery::any(), ['reason' => $reason])
            ->andReturn($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('handleResponse')
            ->once()
            ->with(Mockery::type(PayPalSubscriptionResponseHandler::class))
            ->andReturn($responseHandler);

        // Act
        $result = $this->payPalSubscriptionService->suspendSubscription($subscriptionId, $reason);

        // Assert
        $this->assertInstanceOf(PayPalSubscriptionResponseHandler::class, $result);
        $this->assertEquals('SUSPENDED', $result->toArray()['status']);
    }

    public function testSuspendSubscriptionFailure(): void
    {
        // Arrange
        $subscriptionId = 'test_subscription_id';
        $this->payPalSubscriptionService->shouldReceive('makeRequest')
            ->once()
            ->with('POST', Mockery::any(), [])
            ->andThrow(new Exception('Request error'));

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed to suspend PayPal subscription '{$subscriptionId}': Request error");

        // Act
        $this->payPalSubscriptionService->suspendSubscription($subscriptionId);
    }

    public function testCancelSubscriptionSuccess(): void
    {
        // Arrange
        $subscriptionId = 'test_subscription_id';
        $reason = 'Test cancellation';
        $mockResponse = ['id' => $subscriptionId, 'status' => 'CANCELLED'];
        $responseHandler = new PayPalSubscriptionResponseHandler($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('makeRequest')
            ->once()
            ->with('POST', Mockery::any(), ['reason' => $reason])
            ->andReturn($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('handleResponse')
            ->once()
            ->with(Mockery::type(PayPalSubscriptionResponseHandler::class))
            ->andReturn($responseHandler);

        // Act
        $result = $this->payPalSubscriptionService->cancelSubscription($subscriptionId, $reason);

        // Assert
        $this->assertInstanceOf(PayPalSubscriptionResponseHandler::class, $result);
        $this->assertEquals('CANCELLED', $result->toArray()['status']);
    }

    public function testCancelSubscriptionFailure(): void
    {
        // Arrange
        $subscriptionId = 'test_subscription_id';
        $this->payPalSubscriptionService->shouldReceive('makeRequest')
            ->once()
            ->with('POST', Mockery::any(), [])
            ->andThrow(new Exception('Request error'));

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed to cancel PayPal subscription '{$subscriptionId}': Request error");

        // Act
        $this->payPalSubscriptionService->cancelSubscription($subscriptionId);
    }

    public function testActivateSubscriptionSuccess(): void
    {
        // Arrange
        $subscriptionId = 'test_subscription_id';
        $reason = 'Test activation';
        $mockResponse = ['id' => $subscriptionId, 'status' => 'ACTIVE'];
        $responseHandler = new PayPalSubscriptionResponseHandler($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('makeRequest')
            ->once()
            ->with('POST', Mockery::any(), ['reason' => $reason])
            ->andReturn($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('handleResponse')
            ->once()
            ->with(Mockery::type(PayPalSubscriptionResponseHandler::class))
            ->andReturn($responseHandler);

        // Act
        $result = $this->payPalSubscriptionService->activateSubscription($subscriptionId, $reason);

        // Assert
        $this->assertInstanceOf(PayPalSubscriptionResponseHandler::class, $result);
        $this->assertEquals('ACTIVE', $result->toArray()['status']);
    }

    public function testActivateSubscriptionFailure(): void
    {
        // Arrange
        $subscriptionId = 'test_subscription_id';
        $this->payPalSubscriptionService->shouldReceive('makeRequest')
            ->once()
            ->with('POST', Mockery::any(), [])
            ->andThrow(new Exception('Request error'));

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed to activate PayPal subscription '{$subscriptionId}': Request error");

        // Act
        $this->payPalSubscriptionService->activateSubscription($subscriptionId);
    }

    public function testCapturePaymentSuccess(): void
    {
        // Arrange
        $subscriptionId = 'test_subscription_id';
        $currencyCode = 'USD';
        $value = '10.00';
        $noteToPayer = 'Test capture';

        $data = [
            'amount' => [
                'currency_code' => $currencyCode,
                'value' => $value,
            ],
            'note_to_payer' => $noteToPayer,
        ];
        $mockResponse = ['id' => 'test_capture_id'];
        $responseHandler = new PayPalSubscriptionResponseHandler($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('makeRequest')
            ->once()
            ->with('POST', Mockery::any(), $data)
            ->andReturn($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('handleResponse')
            ->once()
            ->with(Mockery::type(PayPalSubscriptionResponseHandler::class))
            ->andReturn($responseHandler);

        // Act
        $result = $this->payPalSubscriptionService->capturePayment($subscriptionId, $currencyCode, $value, $noteToPayer);

        // Assert
        $this->assertInstanceOf(PayPalSubscriptionResponseHandler::class, $result);
        $this->assertEquals('test_capture_id', $result->toArray()['id']);
    }

    public function testCapturePaymentFailure(): void
    {
        // Arrange
        $subscriptionId = 'test_subscription_id';
        $currencyCode = 'USD';
        $value = '10.00';
        $this->payPalSubscriptionService->shouldReceive('makeRequest')
            ->once()
            ->with('POST', Mockery::any(), Mockery::any())
            ->andThrow(new Exception('Request error'));

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed to capture payment for subscription '{$subscriptionId}': Request error");

        // Act
        $this->payPalSubscriptionService->capturePayment($subscriptionId, $currencyCode, $value);
    }

    public function testListTransactionsSuccess(): void
    {
        // Arrange
        $subscriptionId = 'test_subscription_id';
        $startTime = '2023-01-01T00:00:00Z';
        $endTime = '2023-01-31T23:59:59Z';
        $mockResponse = ['transactions' => []];
        $responseHandler = new PayPalSubscriptionResponseHandler($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('makeRequest')
            ->once()
            ->with('GET', Mockery::any())
            ->andReturn($mockResponse);

        $this->payPalSubscriptionService->shouldReceive('handleResponse')
            ->once()
            ->with(Mockery::type(PayPalSubscriptionResponseHandler::class))
            ->andReturn($responseHandler);

        // Act
        $result = $this->payPalSubscriptionService->listTransactions($subscriptionId, $startTime, $endTime);

        // Assert
        $this->assertInstanceOf(PayPalSubscriptionResponseHandler::class, $result);
        $this->assertIsArray($result->toArray()['transactions']);
    }

    public function testListTransactionsFailure(): void
    {
        // Arrange
        $subscriptionId = 'test_subscription_id';
        $startTime = '2023-01-01T00:00:00Z';
        $endTime = '2023-01-31T23:59:59Z';
        $this->payPalSubscriptionService->shouldReceive('makeRequest')
            ->once()
            ->with('GET', Mockery::any())
            ->andThrow(new Exception('Request error'));

        // Assert
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Failed to list transactions for subscription '{$subscriptionId}': Request error");

        // Act
        $this->payPalSubscriptionService->listTransactions($subscriptionId, $startTime, $endTime);
    }
}