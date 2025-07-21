<?php

namespace Tests\Unit\Services\Payment\Stripe;

use App\Enums\Order\OrderItemable;
use App\Enums\Payment\PaymentGateway;
use App\Enums\Transaction\TransactionStatus;
use App\Exceptions\PaymentGateway\StripeRequestException;
use App\Exceptions\Product\ProductHealthException;
use App\Models\Currency;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Site;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Order\OrderTransactionService;
use App\Services\Payment\Stripe\StripeBaseOrderService;
use App\Services\Payment\Stripe\StripeOrderService;
use App\Services\Payment\Stripe\StripeService;
use App\Services\Payment\Stripe\StripeSubscriptionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class StripeOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private StripeOrderService $stripeOrderService;
    private MockInterface $stripeSubscriptionService;
    private MockInterface $stripeService;
    private MockInterface $orderTransactionService;
    private MockInterface $stripeCheckoutService;
    private User $user;
    private Site $site;
    private Currency $currency;


    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->site = Site::factory()->create();
        $this->currency = Currency::factory()->create(['code' => 'USD']);

        $this->stripeSubscriptionService = Mockery::mock(StripeSubscriptionService::class);
        $this->stripeService = Mockery::mock(StripeService::class);
        $this->orderTransactionService = Mockery::mock(OrderTransactionService::class);
        $this->stripeCheckoutService = Mockery::mock(StripeService::class);


        $this->app->bind(StripeSubscriptionService::class, function () {
            return $this->stripeSubscriptionService;
        });

        $this->app->bind(StripeService::class, function () {
            return $this->stripeService;
        });

        $this->app->bind(OrderTransactionService::class, function () {
            return $this->orderTransactionService;
        });

        $this->stripeOrderService = new StripeOrderService(
            $this->stripeSubscriptionService,
            $this->stripeService,
            $this->orderTransactionService,
            $this->stripeCheckoutService
        );

        $this->stripeOrderService->setUser($this->user);
        $this->stripeOrderService->setSite($this->site);

    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCreateProductOrderItem_success(): void
    {
        $product = Product::factory()->create([
            'title' => 'Test Product',
            'description' => 'Test Description',
        ]);
        $orderItem = OrderItem::factory()->create([
            'order_itemable_type' => OrderItemable::PRODUCT,
            'order_itemable_id' => $product->id,
            'quantity' => 2,
            'price' => 10.00,
        ]);
        $orderItem->setRelation('orderItemable', $product);
        $price = $orderItem->getOrderItemPrice();
        $price->currency = $this->currency;

        $expectedResult = [
            'price_data' => [
                'currency' => $price->currency->code,
                'product_data' => [
                    'name' => $product->title,
                    'description' => $product->description,
                ],
                'unit_amount' => $orderItem->calculateTotalPrice() * 100,
            ],
            'quantity' => $orderItem->quantity,
        ];

        $result = $this->stripeOrderService->createProductOrderItem($orderItem);

        $this->assertEquals($expectedResult, $result);
    }

    public function testCreateProductOrderItem_productNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Product not found for order item');

        $orderItem = OrderItem::factory()->create([
            'order_itemable_type' => OrderItemable::PRODUCT,
            'order_itemable_id' => 999, // Non-existent product ID
        ]);

        $this->stripeOrderService->createProductOrderItem($orderItem);
    }

    public function testCreateProductOrderItem_productHealthException(): void
    {
        $this->expectException(ProductHealthException::class);

        $product = Product::factory()->create();
        $orderItem = OrderItem::factory()->create([
            'order_itemable_type' => OrderItemable::PRODUCT,
            'order_itemable_id' => $product->id,
        ]);
        $orderItem->setRelation('orderItemable', $product);

        $product->shouldReceive('healthCheck')->andReturn(['unhealthy' => ['count' => 1]]);

        $this->stripeOrderService->createProductOrderItem($orderItem);
    }

    public function testCreateOrderItem_product(): void
    {
        $product = Product::factory()->create();
        $orderItem = OrderItem::factory()->create([
            'order_itemable_type' => OrderItemable::PRODUCT,
            'order_itemable_id' => $product->id,
        ]);

        $stripeOrderService = \Mockery::mock(StripeOrderService::class, [
            $this->stripeSubscriptionService,
            $this->stripeService,
            $this->orderTransactionService,
            $this->stripeCheckoutService,
        ])->makePartial()->shouldAllowMockingProtectedMethods();

        $stripeOrderService->shouldReceive('createProductOrderItem')
            ->once()
            ->with($orderItem)
            ->andReturn(['test' => 'data']);

        $result = $stripeOrderService->createOrderItem($orderItem);

        $this->assertEquals(['test' => 'data'], $result);
    }

    public function testCreateOrderItem_unknownOrderItemable(): void
    {
        $orderItem = OrderItem::factory()->create([
            'order_itemable_type' => 'UnknownType',
        ]);

        $result = $this->stripeOrderService->createOrderItem($orderItem);

        $this->assertNull($result);
    }

    public function testCreateCheckoutSession_success(): void
    {
        $order = Order::factory()->create(['site_id' => $this->site->id, 'currency_id' => $this->currency->id]);
        $transaction = Transaction::factory()->create();
        $product = Product::factory()->create();
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'order_itemable_type' => OrderItemable::PRODUCT,
            'order_itemable_id' => $product->id
        ]);
        $order->items()->save($orderItem);
        $stripePaymentGateway = $this->site->activePaymentGatewayByName(PaymentGateway::STRIPE)->first()?->pivot;
        $return_url = $stripePaymentGateway->settings['return_url'] ?? null;


        $mockResponse = (object)['id' => 'session_123'];

        $this->orderTransactionService->shouldReceive('setUser')->with($this->user)->once();
        $this->orderTransactionService->shouldReceive('setSite')->with($this->site)->once();
        $this->stripeService->shouldReceive('setApiKey')->once();
        $this->stripeService->shouldReceive('setPublishableKey')->once();
        $this->stripeCheckoutService->shouldReceive('createOneTimePaymentSession')->once()->andReturn($mockResponse);
        $this->orderTransactionService->shouldReceive('updateTransaction')->once()->with(
            $order,
            $transaction,
            Mockery::subset([
                'currency_code' => $this->currency->code,
                'status' => TransactionStatus::PROCESSING,
                'amount' => $order->calculateFinalTotal(),
            ])
        );

        $result = $this->stripeOrderService->createCheckoutSession($order, $transaction);

        $this->assertEquals($mockResponse, $result);
    }

    public function testCreateCheckoutSession_stripeApiException(): void
    {
        $this->expectException(StripeRequestException::class);

        $order = Order::factory()->create(['site_id' => $this->site->id, 'currency_id' => $this->currency->id]);
        $transaction = Transaction::factory()->create();
        $product = Product::factory()->create();
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'order_itemable_type' => OrderItemable::PRODUCT,
            'order_itemable_id' => $product->id
        ]);
        $order->items()->save($orderItem);

        $stripePaymentGateway = $this->site->activePaymentGatewayByName(PaymentGateway::STRIPE)->first()?->pivot;
        $return_url = $stripePaymentGateway->settings['return_url'] ?? null;

        $this->orderTransactionService->shouldReceive('setUser')->with($this->user)->once();
        $this->orderTransactionService->shouldReceive('setSite')->with($this->site)->once();
        $this->stripeService->shouldReceive('setApiKey')->once();
        $this->stripeService->shouldReceive('setPublishableKey')->once();
        $this->stripeCheckoutService->shouldReceive('createOneTimePaymentSession')
            ->once()
            ->andThrow(new \Stripe\Exception\ApiErrorException('Stripe error', 400, 'stripe_code'));

        $this->orderTransactionService->shouldReceive('updateTransaction')->once()->with(
            $order,
            $transaction,
            Mockery::subset([
                'currency_code' => $this->currency->code,
                'status' => TransactionStatus::FAILED,
                'amount' => $order->calculateFinalTotal(),
            ])
        );


        $this->stripeOrderService->createCheckoutSession($order, $transaction);

    }

    public function testCreateCheckoutSession_genericException(): void
    {
        $this->expectException(StripeRequestException::class);

        $order = Order::factory()->create(['site_id' => $this->site->id, 'currency_id' => $this->currency->id]);
        $transaction = Transaction::factory()->create();
        $product = Product::factory()->create();
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'order_itemable_type' => OrderItemable::PRODUCT,
            'order_itemable_id' => $product->id
        ]);
        $order->items()->save($orderItem);

        $stripePaymentGateway = $this->site->activePaymentGatewayByName(PaymentGateway::STRIPE)->first()?->pivot;
        $return_url = $stripePaymentGateway->settings['return_url'] ?? null;

        $this->orderTransactionService->shouldReceive('setUser')->with($this->user)->once();
        $this->orderTransactionService->shouldReceive('setSite')->with($this->site)->once();
        $this->stripeService->shouldReceive('setApiKey')->once();
        $this->stripeService->shouldReceive('setPublishableKey')->once();
        $this->stripeCheckoutService->shouldReceive('createOneTimePaymentSession')
            ->once()
            ->andThrow(new \Exception('Generic error'));

        $this->orderTransactionService->shouldReceive('updateTransaction')->never();

        $this->stripeOrderService->createCheckoutSession($order, $transaction);
    }

    public function testHandleOneTimePaymentApproval_success(): void
    {
        $order = Order::factory()->create();
        $transaction = Transaction::factory()->create();
        $data = ['id' => 'sub_123'];
        $response = ['status' => 'active'];

        $this->orderTransactionService->shouldReceive('setUser')->with($this->user)->once();
        $this->orderTransactionService->shouldReceive('setSite')->with($this->site)->once();
        $this->stripeService->shouldReceive('setApiKey')->once();
        $this->stripeService->shouldReceive('setPublishableKey')->once();
        $this->stripeSubscriptionService->shouldReceive('retrieveSubscription')
            ->with('sub_123')
            ->once()
            ->andReturn($response);

        $this->orderTransactionService->shouldReceive('updateTransaction')
            ->with($order, $transaction, Mockery::subset(['status' => TransactionStatus::COMPLETED, 'transaction_data' => $response]))
            ->once();

        $result = $this->stripeOrderService->handleOneTimePaymentApproval($order, $transaction, $data);

        $this->assertEquals($response, $result);
    }

    public function testHandleOneTimePaymentApproval_missingId(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Subscription ID is missing in the approval data: []');

        $order = Order::factory()->create();
        $transaction = Transaction::factory()->create();
        $data = [];

        $this->orderTransactionService->shouldReceive('setUser')->with($this->user)->once();
        $this->orderTransactionService->shouldReceive('setSite')->with($this->site)->once();
        $this->orderTransactionService->shouldReceive('updateTransaction')
            ->with($order, $transaction, Mockery::subset(['status' => TransactionStatus::FAILED, 'transaction_data' => []]))
            ->once();

        $this->stripeOrderService->handleOneTimePaymentApproval($order, $transaction, $data);
    }

    public function testHandleOneTimePaymentApproval_retrieveSubscriptionFails(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error retrieving PayPal subscription: null');

        $order = Order::factory()->create();
        $transaction = Transaction::factory()->create();
        $data = ['id' => 'sub_123'];

        $this->orderTransactionService->shouldReceive('setUser')->with($this->user)->once();
        $this->orderTransactionService->shouldReceive('setSite')->with($this->site)->once();
        $this->stripeService->shouldReceive('setApiKey')->once();
        $this->stripeService->shouldReceive('setPublishableKey')->once();
        $this->stripeSubscriptionService->shouldReceive('retrieveSubscription')
            ->with('sub_123')
            ->once()
            ->andReturn(null);

        $this->orderTransactionService->shouldReceive('updateTransaction')
            ->with($order, $transaction, Mockery::subset(['status' => TransactionStatus::FAILED, 'transaction_data' => null]))
            ->once();

        $this->stripeOrderService->handleOneTimePaymentApproval($order, $transaction, $data);
    }

    public function testHandleOneTimePaymentApproval_stripeApiException(): void
    {
        $this->expectException(StripeRequestException::class);

        $order = Order::factory()->create();
        $transaction = Transaction::factory()->create();
        $data = ['id' => 'sub_123'];

        $this->orderTransactionService->shouldReceive('setUser')->with($this->user)->once();
        $this->orderTransactionService->shouldReceive('setSite')->with($this->site)->once();
        $this->stripeService->shouldReceive('setApiKey')->once();
        $this->stripeService->shouldReceive('setPublishableKey')->once();
        $this->stripeSubscriptionService->shouldReceive('retrieveSubscription')
            ->with('sub_123')
            ->once()
            ->andThrow(new \Stripe\Exception\ApiErrorException('Stripe error', 400, 'stripe_code'));

        $this->orderTransactionService->shouldReceive('updateTransaction')
            ->with($order, $transaction, Mockery::subset(['status' => TransactionStatus::FAILED]))
            ->once();


        $this->stripeOrderService->handleOneTimePaymentApproval($order, $transaction, $data);
    }

    public function testHandleOneTimePaymentApproval_genericException(): void
    {
        $this->expectException(StripeRequestException::class);

        $order = Order::factory()->create();
        $transaction = Transaction::factory()->create();
        $data = ['id' => 'sub_123'];

        $this->orderTransactionService->shouldReceive('setUser')->with($this->user)->once();
        $this->orderTransactionService->shouldReceive('setSite')->with($this->site)->once();
        $this->stripeService->shouldReceive('setApiKey')->once();
        $this->stripeService->shouldReceive('setPublishableKey')->once();
        $this->stripeSubscriptionService->shouldReceive('retrieveSubscription')
            ->with('sub_123')
            ->once()
            ->andThrow(new \Exception('Generic error'));

        $this->orderTransactionService->shouldReceive('updateTransaction')
            ->with($order, $transaction, Mockery::subset(['status' => TransactionStatus::FAILED]))
            ->once();

        $this->stripeOrderService->handleOneTimePaymentApproval($order, $transaction, $data);
    }

    public function testHandleOneTimePaymentCancel_success(): void
    {
        $order = Order::factory()->create();
        $transaction = Transaction::factory()->create();
        $data = ['reason' => 'user_cancelled'];

        $this->orderTransactionService->shouldReceive('setUser')->with($this->user)->once();
        $this->orderTransactionService->shouldReceive('setSite')->with($this->site)->once();
        $this->orderTransactionService->shouldReceive('updateTransaction')
            ->with($order, $transaction, Mockery::subset(['status' => TransactionStatus::CANCELLED, 'transaction_data' => $data]))
            ->once();

        $result = $this->stripeOrderService->handleOneTimePaymentCancel($order, $transaction, $data);

        $this->assertTrue($result);
    }
}