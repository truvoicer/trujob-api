<?php

namespace Tests\Unit\Services\Payment\PayPal;

use App\Enums\Order\OrderItemable;
use App\Enums\Payment\PaymentGatewayEnvironment;
use App\Enums\Price\PriceType;
use App\Enums\Transaction\TransactionStatus;
use App\Exceptions\Product\ProductHealthException;
use App\Models\Currency;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Price;
use App\Models\Product;
use App\Models\Site;
use App\Models\Subscription;
use App\Models\SubscriptionItem;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Order\Transaction\OrderTransactionService;
use App\Services\Payment\PayPal\PayPalSubscriptionOrderService;
use App\Services\Payment\PayPal\Middleware\Billing\PayPalBillingPlanService;
use App\Services\Payment\PayPal\Middleware\Product\PayPalProductService;
use App\Services\Payment\PayPal\Middleware\Subscription\PayPalSubscriptionResponseHandler;
use App\Services\Payment\PayPal\Middleware\Subscription\PayPalSubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PayPalSubscriptionOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private $payPalSubscriptionService;
    private $payPalProductService;
    private $billingPlanService;
    private $orderTransactionService;
    private $payPalSubscriptionOrderService;
    private $user;
    private $site;
    private $product;
    private $price;
    private $order;
    private $orderItem;
    private $transaction;
    private $currency;
    private $subscription;
    private $subscriptionItem;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock dependencies
        $this->payPalSubscriptionService = Mockery::mock(PayPalSubscriptionService::class);
        $this->payPalProductService = Mockery::mock(PayPalProductService::class);
        $this->billingPlanService = Mockery::mock(PayPalBillingPlanService::class);
        $this->orderTransactionService = Mockery::mock(OrderTransactionService::class);

        // Instantiate the service with mocked dependencies
        $this->payPalSubscriptionOrderService = new PayPalSubscriptionOrderService(
            $this->payPalSubscriptionService,
            $this->payPalProductService,
            $this->billingPlanService,
            $this->orderTransactionService
        );

        // Create necessary models
        $this->user = User::factory()->create();
        $this->site = Site::factory()->create();
        $this->currency = Currency::factory()->create(['code' => 'USD']);
        $this->product = Product::factory()->create(['type' => 'digital']);
        $this->subscription = Subscription::factory()->create();
        $this->subscriptionItem = SubscriptionItem::factory()->create(['subscription_id' => $this->subscription->id]);
        $this->price = Price::factory()->create(['product_id' => $this->product->id, 'subscription_id' => $this->subscription->id, 'currency_id' => $this->currency->id]);
        $this->order = Order::factory()->create(['user_id' => $this->user->id, 'site_id' => $this->site->id, 'currency_id' => $this->currency->id]);
        $this->orderItem = OrderItem::factory()->create(['order_id' => $this->order->id, 'order_itemable_id' => $this->product->id, 'order_itemable_type' => OrderItemable::PRODUCT]);
        $this->transaction = Transaction::factory()->create(['order_id' => $this->order->id]);

        // Set the authenticated user
        $this->actingAs($this->user);

        //Set the site user
        $this->payPalSubscriptionOrderService->setUser($this->user);
        $this->payPalSubscriptionOrderService->setSite($this->site);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCreatePayPalProduct(): void
    {
        // Arrange
        $this->payPalProductService->shouldReceive('createProduct')
            ->once()
            ->andReturn(['id' => 'mock_product_id']);

        // Act
        $result = $this->payPalSubscriptionOrderService->createPayPalProduct($this->product);

        // Assert
        $this->assertEquals(['id' => 'mock_product_id'], $result);
    }

    public function testCreatePayPalProductThrowsException(): void
    {
        // Arrange
        $this->payPalProductService->shouldReceive('createProduct')
            ->once()
            ->andThrow(new \Exception('Failed to create product'));

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to create PayPal product');

        // Act
        $this->payPalSubscriptionOrderService->createPayPalProduct($this->product);
    }

    public function testCreatePayPalBillingPlan(): void
    {
        // Arrange
        $paypalProductId = 'mock_product_id';

        $this->billingPlanService->shouldReceive('createPlan')
            ->once()
            ->andReturn(['id' => 'mock_billing_plan_id']);

        // Act
        $result = $this->payPalSubscriptionOrderService->createPayPalBillingPlan(
            $paypalProductId,
            $this->price
        );

        // Assert
        $this->assertEquals(['id' => 'mock_billing_plan_id'], $result);
    }

    public function testCreatePayPalBillingPlanThrowsExceptionWhenSubscriptionNotFound(): void
    {
        // Arrange
        $product = Product::factory()->create(['type' => 'digital']);
        $price = Price::factory()->create(['product_id' => $product->id, 'subscription_id' => null, 'currency_id' => $this->currency->id]);

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Subscription not found for product price');

        // Act
        $this->payPalSubscriptionOrderService->createPayPalBillingPlan(
            'mock_product_id',
            $price
        );
    }

    public function testCreatePayPalBillingPlanThrowsExceptionWhenSubscriptionItemsNotFound(): void
    {
        // Arrange
        $subscription = Subscription::factory()->create();
        $price = Price::factory()->create(['product_id' => $this->product->id, 'subscription_id' => $subscription->id, 'currency_id' => $this->currency->id]);

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('No subscription items found for product price');

        // Act
        $this->payPalSubscriptionOrderService->createPayPalBillingPlan(
            'mock_product_id',
            $price
        );
    }

    public function testCreatePayPalBillingPlanThrowsException(): void
    {
        // Arrange
        $paypalProductId = 'mock_product_id';
        $this->billingPlanService->shouldReceive('createPlan')
            ->once()
            ->andThrow(new \Exception('Failed to create plan'));

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to create PayPal billing plan');

        // Act
        $this->payPalSubscriptionOrderService->createPayPalBillingPlan(
            $paypalProductId,
            $this->price
        );
    }

    public function testCreatePayPalSubscriptionsByProduct(): void
    {
        // Arrange
        $paypalProductId = 'mock_product_id';
        $this->payPalSubscriptionOrderService->shouldReceive('createPayPalBillingPlan')
            ->once()
            ->andReturn(['id' => 'mock_plan_id']);

        $mockResponseHandler = Mockery::mock(PayPalSubscriptionResponseHandler::class);
        $this->payPalSubscriptionService->shouldReceive('createSubscription')
            ->once()
            ->andReturn($mockResponseHandler);

        // Act
        $result = $this->payPalSubscriptionOrderService->createPayPalSubscriptionsByProduct(
            $paypalProductId,
            $this->orderItem
        );

        // Assert
        $this->assertInstanceOf(PayPalSubscriptionResponseHandler::class, $result);
    }

    public function testCreatePayPalSubscriptionsByProductThrowsException(): void
    {
        // Arrange
        $paypalProductId = 'mock_product_id';
        $this->payPalSubscriptionOrderService->shouldReceive('createPayPalBillingPlan')
            ->once()
            ->andReturn(['id' => 'mock_plan_id']);

        $this->payPalSubscriptionService->shouldReceive('createSubscription')
            ->once()
            ->andThrow(new \Exception('Failed to create subscription'));

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to create PayPal subscription');

        // Act
        $this->payPalSubscriptionOrderService->createPayPalSubscriptionsByProduct(
            $paypalProductId,
            $this->orderItem
        );
    }

    public function testCreateProductSubscription(): void
    {
        // Arrange
        $this->payPalSubscriptionOrderService->shouldReceive('createPayPalProduct')
            ->once()
            ->andReturn(['id' => 'mock_product_id']);

        $this->payPalSubscriptionOrderService->shouldReceive('createPayPalSubscriptionsByProduct')
            ->once()
            ->andReturn(Mockery::mock(PayPalSubscriptionResponseHandler::class));

        // Act
        $result = $this->payPalSubscriptionOrderService->createProductSubscription($this->orderItem);

        // Assert
        $this->assertInstanceOf(PayPalSubscriptionResponseHandler::class, $result);
    }

    public function testCreateProductSubscriptionThrowsProductHealthException(): void
    {
        // Arrange
        $product = Mockery::mock(Product::class);
        $orderItem = Mockery::mock(OrderItem::class);
        $orderItem->shouldReceive('getAttribute')->with('order_itemable')->andReturn($product);

        $product->shouldReceive('healthCheck')->once()->andReturn(['unhealthy' => ['count' => 1]]);

        // Assert
        $this->expectException(ProductHealthException::class);

        // Act
        $this->payPalSubscriptionOrderService->createProductSubscription($this->orderItem);
    }

    public function testCreateOrderItemSubscriptionWithProduct(): void
    {
        // Arrange
        $this->payPalSubscriptionOrderService->shouldReceive('createProductSubscription')
            ->once()
            ->andReturn(Mockery::mock(PayPalSubscriptionResponseHandler::class));

        // Act
        $result = $this->payPalSubscriptionOrderService->createOrderItemSubscription($this->orderItem);

        // Assert
        $this->assertInstanceOf(PayPalSubscriptionResponseHandler::class, $result);
    }

    public function testCreateOrderItemSubscriptionThrowsExceptionForInvalidType(): void
    {
        // Arrange
        $orderItem = new OrderItem(['order_itemable_type' => 'invalid']);

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid order item type');

        // Act
        $this->payPalSubscriptionOrderService->createOrderItemSubscription($orderItem);
    }

    public function testCreateSubscription(): void
    {
        // Arrange
        $sitePaypalSettings = [
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
            'webhook_id' => 'test_webhook_id',
        ];

        $this->site->paymentGateways()->attach(1, ['name' => 'paypal', 'environment' => PaymentGatewayEnvironment::SANDBOX, 'settings' => $sitePaypalSettings]);
        $this->orderTransactionService->shouldReceive('setUser')->once()->with($this->user);
        $this->orderTransactionService->shouldReceive('setSite')->once()->with($this->site);
        $this->payPalSubscriptionOrderService->shouldReceive('createOrderItemSubscription')->once()->with($this->orderItem)->andReturn(new PayPalSubscriptionResponseHandler(['status' => 'PROCESSING', 'isSuccess' => true]));
        $this->orderTransactionService->shouldReceive('updateTransaction')->twice();
        $this->transaction->status = TransactionStatus::PROCESSING;
        $this->transaction->save();
        // Act
        $response = $this->payPalSubscriptionOrderService->createSubscription($this->order, $this->transaction);

        // Assert
        $this->assertInstanceOf(PayPalSubscriptionResponseHandler::class, $response);
        $this->assertEquals(TransactionStatus::PROCESSING->value, $this->transaction->status->value);
    }

    public function testCreateSubscriptionFailed(): void
    {
        // Arrange
        $sitePaypalSettings = [
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
            'webhook_id' => 'test_webhook_id',
        ];

        $this->site->paymentGateways()->attach(1, ['name' => 'paypal', 'environment' => PaymentGatewayEnvironment::SANDBOX, 'settings' => $sitePaypalSettings]);
        $this->orderTransactionService->shouldReceive('setUser')->once()->with($this->user);
        $this->orderTransactionService->shouldReceive('setSite')->once()->with($this->site);
        $this->payPalSubscriptionOrderService->shouldReceive('createOrderItemSubscription')->once()->with($this->orderItem)->andReturn(new PayPalSubscriptionResponseHandler(['status' => 'FAILED', 'isSuccess' => false, 'errorMessage' => 'test error message', 'errorDetails' => []]));
        $this->orderTransactionService->shouldReceive('updateTransaction')->once();
        $this->transaction->status = TransactionStatus::FAILED;
        $this->transaction->save();

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error creating PayPal order: test error message Details: []');

        // Act
        $this->payPalSubscriptionOrderService->createSubscription($this->order, $this->transaction);
    }

    public function testHandleSubscriptionApproval(): void
    {
        // Arrange
        $sitePaypalSettings = [
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
            'webhook_id' => 'test_webhook_id',
        ];
        $this->site->paymentGateways()->attach(1, ['name' => 'paypal', 'environment' => PaymentGatewayEnvironment::SANDBOX, 'settings' => $sitePaypalSettings]);

        $data = ['subscriptionID' => 'test_subscription_id'];
        $this->orderTransactionService->shouldReceive('setUser')->once()->with($this->user);
        $this->orderTransactionService->shouldReceive('setSite')->once()->with($this->site);
        $this->payPalSubscriptionService->shouldReceive('showSubscription')->once()->with('test_subscription_id')->andReturn(new PayPalSubscriptionResponseHandler(['status' => 'COMPLETED', 'isSuccess' => true]));
        $this->orderTransactionService->shouldReceive('updateTransaction')->once();

        // Act
        $response = $this->payPalSubscriptionOrderService->handleSubscriptionApproval($this->order, $this->transaction, $data);

        // Assert
        $this->assertInstanceOf(PayPalSubscriptionResponseHandler::class, $response);
        $this->assertEquals(TransactionStatus::COMPLETED->value, $this->transaction->status->value);
    }

    public function testHandleSubscriptionApprovalFailed(): void
    {
        // Arrange
        $sitePaypalSettings = [
            'client_id' => 'test_client_id',
            'client_secret' => 'test_client_secret',
            'webhook_id' => 'test_webhook_id',
        ];
        $this->site->paymentGateways()->attach(1, ['name' => 'paypal', 'environment' => PaymentGatewayEnvironment::SANDBOX, 'settings' => $sitePaypalSettings]);

        $data = ['subscriptionID' => 'test_subscription_id'];
        $this->orderTransactionService->shouldReceive('setUser')->once()->with($this->user);
        $this->orderTransactionService->shouldReceive('setSite')->once()->with($this->site);
        $this->payPalSubscriptionService->shouldReceive('showSubscription')->once()->with('test_subscription_id')->andReturn(new PayPalSubscriptionResponseHandler(['status' => 'FAILED', 'isSuccess' => false, 'response' => ['error' => 'test error']]));
        $this->orderTransactionService->shouldReceive('updateTransaction')->once();

        // Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error retrieving PayPal subscription: {"error":"test error"}');

        // Act
        $this->payPalSubscriptionOrderService->handleSubscriptionApproval($this->order, $this->transaction, $data);
    }

    public function testHandleSubscriptionCancel(): void
    {
        // Arrange
        $data = ['subscriptionID' => 'test_subscription_id'];
        $this->orderTransactionService->shouldReceive('setUser')->once()->with($this->user);
        $this->orderTransactionService->shouldReceive('setSite')->once()->with($this->site);
        $this->orderTransactionService->shouldReceive('updateTransaction')->once();

        // Act
        $result = $this->payPalSubscriptionOrderService->handleSubscriptionCancel($this->order, $this->transaction, $data);

        // Assert
        $this->assertTrue($result);
        $this->assertEquals(TransactionStatus::CANCELLED->value, $this->transaction->status->value);
    }
}