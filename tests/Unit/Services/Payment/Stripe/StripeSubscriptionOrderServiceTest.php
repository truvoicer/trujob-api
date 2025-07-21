<?php

namespace Tests\Unit\Services\Payment\Stripe;

use App\Enums\Order\OrderItemable;
use App\Enums\Payment\PaymentGateway;
use App\Enums\Price\PriceType;
use App\Enums\Subscription\SubscriptionIntervalUnit;
use App\Enums\Subscription\SubscriptionTenureType;
use App\Enums\Transaction\TransactionStatus;
use App\Exceptions\PaymentGateway\StripeRequestException;
use App\Models\Currency;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Price;
use App\Models\Product;
use App\Models\Site;
use App\Models\SubscriptionItem;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Payment\Stripe\StripeBaseOrderService;
use App\Services\Payment\Stripe\StripeCheckoutService;
use App\Services\Payment\Stripe\StripeSubscriptionOrderService;
use App\Services\Payment\Stripe\StripeSubscriptionService;
use App\Services\Order\OrderTransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;
use Mockery\MockInterface;
use Stripe\Checkout\Session;
use Stripe\Subscription;

class StripeSubscriptionOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private MockInterface $stripeCheckoutServiceMock;
    private MockInterface $stripeSubscriptionServiceMock;
    private MockInterface $orderTransactionServiceMock;
    private StripeSubscriptionOrderService $stripeSubscriptionOrderService;
    private User $user;
    private Site $site;
    private Order $order;
    private Transaction $transaction;
    private Product $product;
    private Price $price;
    private Currency $currency;
    private SubscriptionItem $subscriptionItem;
    private OrderItem $orderItem;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up mock dependencies
        $this->stripeCheckoutServiceMock = Mockery::mock(StripeCheckoutService::class);
        $this->stripeSubscriptionServiceMock = Mockery::mock(StripeSubscriptionService::class);
        $this->orderTransactionServiceMock = Mockery::mock(OrderTransactionService::class);

        // Instantiate the service with mocks
        $this->app->bind(StripeCheckoutService::class, fn () => $this->stripeCheckoutServiceMock);
        $this->app->bind(StripeSubscriptionService::class, fn () => $this->stripeSubscriptionServiceMock);
        $this->app->bind(OrderTransactionService::class, fn () => $this->orderTransactionServiceMock);
        $this->stripeSubscriptionOrderService = $this->app->make(StripeSubscriptionOrderService::class);

        // Create a user and site
        $this->user = User::factory()->create();
        $this->site = Site::factory()->create();

        // Create a product, price, currency and subscriptionItem
        $this->currency = Currency::factory()->create(['code' => 'USD']);
        $this->product = Product::factory()->create(['title' => 'Test Product', 'description' => 'Test Description']);
        $this->subscriptionItem = SubscriptionItem::factory()->create(['tenure_type' => SubscriptionTenureType::TRIAL->value, 'frequency_interval_unit' => SubscriptionIntervalUnit::DAY, 'total_cycles' => 1]);
        $this->price = Price::factory()->create(['priceable_id' => $this->product->id, 'priceable_type' => Product::class, 'currency_id' => $this->currency->id]);
        $this->price->subscription()->associate($this->subscriptionItem);
        $this->price->save();

        // Create an order and transaction
        $this->order = Order::factory()->create(['site_id' => $this->site->id, 'user_id' => $this->user->id, 'currency_id' => $this->currency->id]);
        $this->orderItem = OrderItem::factory()->create(['order_id' => $this->order->id, 'order_itemable_id' => $this->product->id, 'order_itemable_type' => Product::class, 'quantity' => 1, 'price' => 100]);
        $this->transaction = Transaction::factory()->create(['order_id' => $this->order->id]);

        // Set user and site to the service
        $this->stripeSubscriptionOrderService->setUser($this->user);
        $this->stripeSubscriptionOrderService->setSite($this->site);

        // Set stripe service mock on base class (since initializeStripeService is protected)
        $reflection = new \ReflectionClass(StripeBaseOrderService::class);
        $reflectionProperty = $reflection->getProperty('stripeService');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($this->stripeSubscriptionOrderService, $this->stripeSubscriptionServiceMock);

        //Mocking site activePaymentGatewayByName
        $siteMock = Mockery::mock(Site::class);
        $siteMock->shouldReceive('activePaymentGatewayByName')->with(PaymentGateway::STRIPE)->andReturnSelf();
        $siteMock->shouldReceive('first')->andReturn((object)['pivot' => ['settings' => ['return_url' => 'https://example.com/return']]]);
        $this->app->instance(Site::class, $siteMock);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCreateProductSubscription(): void
    {
        $session = new Session('session_id');
        $this->stripeCheckoutServiceMock->shouldReceive('createSubscriptionSession')->once()->andReturn($session);

        $result = $this->stripeSubscriptionOrderService->createProductSubscription($this->orderItem);

        $this->assertInstanceOf(Session::class, $result);
    }

    public function testCreateOrderItemSubscriptionWithProduct(): void
    {
        $session = new Session('session_id');
        $this->stripeCheckoutServiceMock->shouldReceive('createSubscriptionSession')->once()->andReturn($session);

        $result = $this->stripeSubscriptionOrderService->createOrderItemSubscription($this->orderItem);

        $this->assertInstanceOf(Session::class, $result);
    }

    public function testCreateOrderItemSubscriptionWithInvalidType(): void
    {
        $this->orderItem->order_itemable_type = 'InvalidType';
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid order item type');

        $this->stripeSubscriptionOrderService->createOrderItemSubscription($this->orderItem);
    }

    public function testCreateSubscriptionSuccess(): void
    {
        $session = new Session('session_id');

        $this->orderTransactionServiceMock->shouldReceive('setUser')->once()->with($this->user);
        $this->orderTransactionServiceMock->shouldReceive('setSite')->once()->with($this->site);
        $this->orderTransactionServiceMock->shouldReceive('updateTransaction')->once();
        $this->stripeCheckoutServiceMock->shouldReceive('createSubscriptionSession')->once()->andReturn($session);

        $result = $this->stripeSubscriptionOrderService->createSubscription($this->order, $this->transaction);

        $this->assertInstanceOf(Session::class, $result);
    }

    public function testCreateSubscriptionException(): void
    {
        $this->orderTransactionServiceMock->shouldReceive('setUser')->once()->with($this->user);
        $this->orderTransactionServiceMock->shouldReceive('setSite')->once()->with($this->site);
        $this->orderTransactionServiceMock->shouldReceive('updateTransaction')->once();
        $this->stripeCheckoutServiceMock->shouldReceive('createSubscriptionSession')->once()->andThrow(new \Exception('Test Exception'));
        $this->expectException(StripeRequestException::class);

        $this->stripeSubscriptionOrderService->createSubscription($this->order, $this->transaction);
    }

    public function testHandleSubscriptionApprovalSuccess(): void
    {
        $subscription = new Subscription('subscription_id');
        $data = ['id' => 'subscription_id'];

        $this->orderTransactionServiceMock->shouldReceive('setUser')->once()->with($this->user);
        $this->orderTransactionServiceMock->shouldReceive('setSite')->once()->with($this->site);
        $this->stripeSubscriptionServiceMock->shouldReceive('retrieveSubscription')->once()->with($data['id'])->andReturn($subscription);
        $this->orderTransactionServiceMock->shouldReceive('updateTransaction')->once();

        $result = $this->stripeSubscriptionOrderService->handleSubscriptionApproval($this->order, $this->transaction, $data);

        $this->assertEquals($subscription, $result);
    }

    public function testHandleSubscriptionApprovalMissingId(): void
    {
        $data = [];

        $this->orderTransactionServiceMock->shouldReceive('setUser')->once()->with($this->user);
        $this->orderTransactionServiceMock->shouldReceive('setSite')->once()->with($this->site);
        $this->orderTransactionServiceMock->shouldReceive('updateTransaction')->once();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Subscription ID is missing in the approval data: []');

        $this->stripeSubscriptionOrderService->handleSubscriptionApproval($this->order, $this->transaction, $data);
    }

    public function testHandleSubscriptionApprovalRetrieveSubscriptionFailure(): void
    {
        $data = ['id' => 'subscription_id'];
        $this->orderTransactionServiceMock->shouldReceive('setUser')->once()->with($this->user);
        $this->orderTransactionServiceMock->shouldReceive('setSite')->once()->with($this->site);
        $this->stripeSubscriptionServiceMock->shouldReceive('retrieveSubscription')->once()->with($data['id'])->andReturn(false);
        $this->orderTransactionServiceMock->shouldReceive('updateTransaction')->once();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error retrieving PayPal subscription: false');

        $this->stripeSubscriptionOrderService->handleSubscriptionApproval($this->order, $this->transaction, $data);
    }

    public function testHandleSubscriptionApprovalException(): void
    {
        $data = ['id' => 'subscription_id'];
        $this->orderTransactionServiceMock->shouldReceive('setUser')->once()->with($this->user);
        $this->orderTransactionServiceMock->shouldReceive('setSite')->once()->with($this->site);
        $this->stripeSubscriptionServiceMock->shouldReceive('retrieveSubscription')->once()->with($data['id'])->andThrow(new \Exception('Test Exception'));
        $this->orderTransactionServiceMock->shouldReceive('updateTransaction')->once();

        $this->expectException(StripeRequestException::class);

        $this->stripeSubscriptionOrderService->handleSubscriptionApproval($this->order, $this->transaction, $data);
    }

    public function testHandleSubscriptionCancel(): void
    {
        $data = ['status' => 'canceled'];

        $this->orderTransactionServiceMock->shouldReceive('setUser')->once()->with($this->user);
        $this->orderTransactionServiceMock->shouldReceive('setSite')->once()->with($this->site);
        $this->orderTransactionServiceMock->shouldReceive('updateTransaction')->once();

        $result = $this->stripeSubscriptionOrderService->handleSubscriptionCancel($this->order, $this->transaction, $data);

        $this->assertTrue($result);
    }
}