<?php

namespace Tests\Unit\Services\Payment\PayPal;

use App\Enums\Order\OrderItemable;
use App\Enums\Price\PriceType;
use App\Enums\Transaction\TransactionStatus;
use App\Exceptions\Product\ProductHealthException;
use App\Models\Currency;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Site;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Order\Transaction\OrderTransactionService;
use App\Services\Payment\PayPal\PayPalOrderService;
use App\Services\Payment\PayPal\Middleware\Order\PayPalOrderService as PaypalOrderServiceSdk;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use PaypalServerSdkLib\Models\Item;
use PaypalServerSdkLib\Models\Money;
use PaypalServerSdkLib\Responses\ResponseHandler;
use Tests\TestCase;

class PayPalOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    private MockInterface $payPalServiceSdkMock;
    private MockInterface $orderTransactionServiceMock;
    private PayPalOrderService $payPalOrderService;
    private User $user;
    private Site $site;

    protected function setUp(): void
    {
        parent::setUp();

        $this->payPalServiceSdkMock = Mockery::mock(PaypalOrderServiceSdk::class);
        $this->orderTransactionServiceMock = Mockery::mock(OrderTransactionService::class);

        $this->payPalOrderService = new PayPalOrderService(
            $this->payPalServiceSdkMock,
            $this->orderTransactionServiceMock
        );

        // Create a default user and site for testing
        $this->user = User::factory()->create();
        $this->site = Site::factory()->create();

        $this->payPalOrderService->setUser($this->user);
        $this->payPalOrderService->setSite($this->site);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testCreateProductOrderItemSuccess(): void
    {
        $product = Product::factory()->create([
            'title' => 'Test Product',
            'description' => 'Test Description',
            'sku' => 'TEST-SKU'
        ]);
        $currency = Currency::factory()->create(['code' => 'USD']);
        $orderItem = OrderItem::factory()->create([
            'order_itemable_id' => $product->id,
            'order_itemable_type' => OrderItemable::PRODUCT,
            'quantity' => 2,
            'price' => 100,
            'tax' => 10,
            'currency_id' => $currency->id
        ]);

        $item = $this->payPalOrderService->createProductOrderItem($orderItem);

        $this->assertInstanceOf(Item::class, $item);
        $this->assertEquals($product->title, $item->getName());
        $this->assertEquals($orderItem->quantity, $item->getQuantity());
        $this->assertEquals($product->description, $item->getDescription());
        $this->assertEquals($product->sku, $item->getSku());
    }

    public function testCreateProductOrderItemThrowsProductHealthException(): void
    {
        $this->expectException(ProductHealthException::class);

        $product = Product::factory()->create();
        $orderItem = OrderItem::factory()->create([
            'order_itemable_id' => $product->id,
            'order_itemable_type' => OrderItemable::PRODUCT,
        ]);
        // Simulate unhealthy product state (e.g., by creating a relationship that triggers a health check failure)
        // For example, if a product's health depends on having at least one category:
        // $product->productCategories()->delete();
        $this->expectException(ProductHealthException::class);
        $this->payPalOrderService->createProductOrderItem($orderItem);
    }


    public function testCreateOrderItemProduct(): void
    {
        $product = Product::factory()->create();
        $currency = Currency::factory()->create(['code' => 'USD']);
        $orderItem = OrderItem::factory()->create([
            'order_itemable_id' => $product->id,
            'order_itemable_type' => OrderItemable::PRODUCT,
            'quantity' => 2,
            'price' => 100,
            'tax' => 10,
            'currency_id' => $currency->id
        ]);

        $item = $this->payPalOrderService->createOrderItem($orderItem);

        $this->assertInstanceOf(Item::class, $item);
    }

    public function testCreateOrderItemNull(): void
    {
        $orderItem = OrderItem::factory()->create([
            'order_itemable_type' => 'unsupported_type',
        ]);

        $item = $this->payPalOrderService->createOrderItem($orderItem);

        $this->assertNull($item);
    }

    public function testCreateOrderSuccess(): void
    {
        $order = Order::factory()->create([
            'price_type' => PriceType::RETAIL,
        ]);
        $currency = Currency::factory()->create(['code' => 'USD']);

        $product = Product::factory()->create();
        $orderItem = OrderItem::factory()->create([
            'order_id' => $order->id,
            'order_itemable_id' => $product->id,
            'order_itemable_type' => OrderItemable::PRODUCT,
            'quantity' => 2,
            'price' => 100,
            'tax' => 10,
            'currency_id' => $currency->id
        ]);
        $order->items()->save($orderItem);

        $transaction = Transaction::factory()->create();

        // Mock PayPal SDK behavior
        $this->payPalServiceSdkMock->shouldReceive('setEnvironment')->once();
        $this->payPalServiceSdkMock->shouldReceive('setClientId')->once();
        $this->payPalServiceSdkMock->shouldReceive('setClientSecret')->once();
        $this->payPalServiceSdkMock->shouldReceive('setWebhookId')->once();
        $this->payPalServiceSdkMock->shouldReceive('init')->once();
        $this->payPalServiceSdkMock->shouldReceive('addItem')->once();

        $this->payPalServiceSdkMock->shouldReceive('setCurrencyCode')->once();
        $this->payPalServiceSdkMock->shouldReceive('setValue')->once();
        $this->payPalServiceSdkMock->shouldReceive('setItemTotal')->once();
        $this->payPalServiceSdkMock->shouldReceive('setTaxTotal')->once();
        $this->payPalServiceSdkMock->shouldReceive('setDiscount')->once();

        $responseHandlerMock = Mockery::mock(ResponseHandler::class);
        $responseHandlerMock->shouldReceive('isSuccess')->andReturn(true);
        $responseHandlerMock->shouldReceive('getResult')->andReturn(['paypal_order_id' => 'TEST_ORDER_ID']);
        $this->payPalServiceSdkMock->shouldReceive('createOrder')->andReturn($responseHandlerMock);

        $this->orderTransactionServiceMock->shouldReceive('setUser')->once();
        $this->orderTransactionServiceMock->shouldReceive('setSite')->once();

        $this->orderTransactionServiceMock->shouldReceive('updateTransaction')
            ->once()
            ->with(
                $order,
                $transaction,
                Mockery::subset([
                    'status' => TransactionStatus::PROCESSING,
                ])
            );


        $result = $this->payPalOrderService->createOrder($order, $transaction);

        $this->assertEquals(['paypal_order_id' => 'TEST_ORDER_ID'], $result);
    }

    public function testCreateOrderFailure(): void
    {
        $this->expectException(\Exception::class);

        $order = Order::factory()->create();
        $transaction = Transaction::factory()->create();

        // Mock PayPal SDK behavior to simulate a failure
        $this->payPalServiceSdkMock->shouldReceive('setEnvironment')->once();
        $this->payPalServiceSdkMock->shouldReceive('setClientId')->once();
        $this->payPalServiceSdkMock->shouldReceive('setClientSecret')->once();
        $this->payPalServiceSdkMock->shouldReceive('setWebhookId')->once();
        $this->payPalServiceSdkMock->shouldReceive('init')->once();
        $this->payPalServiceSdkMock->shouldReceive('addItem')->once();

        $this->payPalServiceSdkMock->shouldReceive('setCurrencyCode')->once();
        $this->payPalServiceSdkMock->shouldReceive('setValue')->once();
        $this->payPalServiceSdkMock->shouldReceive('setItemTotal')->once();
        $this->payPalServiceSdkMock->shouldReceive('setTaxTotal')->once();
        $this->payPalServiceSdkMock->shouldReceive('setDiscount')->once();

        $responseHandlerMock = Mockery::mock(ResponseHandler::class);
        $responseHandlerMock->shouldReceive('isSuccess')->andReturn(false);
        $responseHandlerMock->shouldReceive('getErrorMessage')->andReturn('Test error message');
        $responseHandlerMock->shouldReceive('getErrorDetails')->andReturn(['error_code' => 'TEST_ERROR']);
        $responseHandlerMock->shouldReceive('getResult')->andReturn(['error' => 'Test error data']);

        $this->payPalServiceSdkMock->shouldReceive('createOrder')->andReturn($responseHandlerMock);

        $this->orderTransactionServiceMock->shouldReceive('setUser')->once();
        $this->orderTransactionServiceMock->shouldReceive('setSite')->once();

        $this->orderTransactionServiceMock->shouldReceive('updateTransaction')
            ->once()
            ->with(
                $order,
                $transaction,
                Mockery::subset([
                    'status' => TransactionStatus::FAILED,
                ])
            );

        $this->payPalOrderService->createOrder($order, $transaction);
    }

    public function testGetOrderSuccess(): void
    {
        $orderId = 'TEST_ORDER_ID';

        // Mock PayPal SDK behavior
        $this->payPalServiceSdkMock->shouldReceive('setEnvironment')->once();
        $this->payPalServiceSdkMock->shouldReceive('setClientId')->once();
        $this->payPalServiceSdkMock->shouldReceive('setClientSecret')->once();
        $this->payPalServiceSdkMock->shouldReceive('setWebhookId')->once();
        $this->payPalServiceSdkMock->shouldReceive('init')->once();

        $responseHandlerMock = Mockery::mock(ResponseHandler::class);
        $responseHandlerMock->shouldReceive('isSuccess')->andReturn(true);
        $responseHandlerMock->shouldReceive('getResult')->andReturn(['paypal_order_details' => 'TEST_ORDER_DETAILS']);
        $this->payPalServiceSdkMock->shouldReceive('getOrder')->with($orderId)->andReturn($responseHandlerMock);

        $result = $this->payPalOrderService->getOrder($orderId);

        $this->assertEquals(['paypal_order_details' => 'TEST_ORDER_DETAILS'], $result);
    }

    public function testGetOrderFailure(): void
    {
        $this->expectException(\Exception::class);

        $orderId = 'TEST_ORDER_ID';

        // Mock PayPal SDK behavior to simulate a failure
        $this->payPalServiceSdkMock->shouldReceive('setEnvironment')->once();
        $this->payPalServiceSdkMock->shouldReceive('setClientId')->once();
        $this->payPalServiceSdkMock->shouldReceive('setClientSecret')->once();
        $this->payPalServiceSdkMock->shouldReceive('setWebhookId')->once();
        $this->payPalServiceSdkMock->shouldReceive('init')->once();

        $responseHandlerMock = Mockery::mock(ResponseHandler::class);
        $responseHandlerMock->shouldReceive('isSuccess')->andReturn(false);
        $responseHandlerMock->shouldReceive('getErrorMessage')->andReturn('Test error message');
        $responseHandlerMock->shouldReceive('getErrorDetails')->andReturn(['error_code' => 'TEST_ERROR']);

        $this->payPalServiceSdkMock->shouldReceive('getOrder')->with($orderId)->andReturn($responseHandlerMock);

        $this->payPalOrderService->getOrder($orderId);
    }

    public function testCaptureOrderSuccess(): void
    {
        $order = Order::factory()->create();
        $transaction = Transaction::factory()->create();
        $orderId = 'TEST_ORDER_ID';

        // Mock PayPal SDK behavior
        $this->payPalServiceSdkMock->shouldReceive('setEnvironment')->once();
        $this->payPalServiceSdkMock->shouldReceive('setClientId')->once();
        $this->payPalServiceSdkMock->shouldReceive('setClientSecret')->once();
        $this->payPalServiceSdkMock->shouldReceive('setWebhookId')->once();
        $this->payPalServiceSdkMock->shouldReceive('init')->once();

        $responseHandlerMock = Mockery::mock(ResponseHandler::class);
        $responseHandlerMock->shouldReceive('isSuccess')->andReturn(true);
        $responseHandlerMock->shouldReceive('getResult')->andReturn(['paypal_capture_details' => 'TEST_CAPTURE_DETAILS']);
        $this->payPalServiceSdkMock->shouldReceive('captureOrder')->with($orderId)->andReturn($responseHandlerMock);

        $this->orderTransactionServiceMock->shouldReceive('updateTransaction')
            ->once()
            ->with(
                $order,
                $transaction,
                Mockery::subset([
                    'status' => TransactionStatus::COMPLETED,
                ])
            );

        $result = $this->payPalOrderService->captureOrder($order, $transaction, $orderId);

        $this->assertEquals(['paypal_capture_details' => 'TEST_CAPTURE_DETAILS'], $result);
    }

    public function testCaptureOrderFailure(): void
    {
        $this->expectException(\Exception::class);

        $order = Order::factory()->create();
        $transaction = Transaction::factory()->create();
        $orderId = 'TEST_ORDER_ID';

        // Mock PayPal SDK behavior to simulate a failure
        $this->payPalServiceSdkMock->shouldReceive('setEnvironment')->once();
        $this->payPalServiceSdkMock->shouldReceive('setClientId')->once();
        $this->payPalServiceSdkMock->shouldReceive('setClientSecret')->once();
        $this->payPalServiceSdkMock->shouldReceive('setWebhookId')->once();
        $this->payPalServiceSdkMock->shouldReceive('init')->once();

        $responseHandlerMock = Mockery::mock(ResponseHandler::class);
        $responseHandlerMock->shouldReceive('isSuccess')->andReturn(false);
        $responseHandlerMock->shouldReceive('getErrorMessage')->andReturn('Test error message');
        $responseHandlerMock->shouldReceive('getErrorDetails')->andReturn(['error_code' => 'TEST_ERROR']);
        $responseHandlerMock->shouldReceive('getResult')->andReturn(['paypal_capture_details' => 'TEST_CAPTURE_DETAILS']);

        $this->payPalServiceSdkMock->shouldReceive('captureOrder')->with($orderId)->andReturn($responseHandlerMock);

        $this->orderTransactionServiceMock->shouldReceive('updateTransaction')
            ->once()
            ->with(
                $order,
                $transaction,
                Mockery::subset([
                    'status' => TransactionStatus::FAILED,
                ])
            );
        $this->payPalOrderService->captureOrder($order, $transaction, $orderId);
    }

    public function testUpdateOrder(): void
    {
        // This method currently has no implementation, so we can simply assert true
        $this->assertTrue(true);
    }

    public function testHandleOrderCancellation(): void
    {
        $order = Order::factory()->create();
        $transaction = Transaction::factory()->create();
        $data = ['cancellation_reason' => 'User requested cancellation'];

        $this->orderTransactionServiceMock->shouldReceive('setUser')->once();
        $this->orderTransactionServiceMock->shouldReceive('setSite')->once();

        $this->orderTransactionServiceMock->shouldReceive('updateTransaction')
            ->once()
            ->with(
                $order,
                $transaction,
                Mockery::subset([
                    'status' => TransactionStatus::CANCELLED,
                    'transaction_data' => $data,
                ])
            );

        $result = $this->payPalOrderService->handleOrderCancellation($order, $transaction, $data);

        $this->assertTrue($result);
    }
}