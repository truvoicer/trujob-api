<?php

namespace Tests\Unit\Services\Product;

use App\Contracts\Product\Product as ProductContract;
use App\Exceptions\Product\ProductHealthException;
use App\Models\Discount;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Repositories\ProductRepository;
use App\Services\Product\ProductProductService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Mockery;
use Tests\TestCase;

class ProductProductServiceTest extends TestCase
{
    protected ProductRepository $productRepository;
    protected ProductProductService $productService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productRepository = Mockery::mock(ProductRepository::class);
        $this->productService = new ProductProductService($this->productRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testFindManyReturnsCollectionOrLengthAwarePaginator(): void
    {
        $sort = 'name';
        $order = 'asc';
        $perPage = 10;
        $page = 1;
        $search = 'test';

        $this->productRepository->shouldReceive('setPagination')->once()->with(true);
        $this->productRepository->shouldReceive('setOrderByColumn')->once()->with($sort);
        $this->productRepository->shouldReceive('setOrderByDir')->once()->with($order);
        $this->productRepository->shouldReceive('setPerPage')->once()->with($perPage);
        $this->productRepository->shouldReceive('setPage')->once()->with($page);
        $this->productRepository->shouldReceive('addWhere')->once()->with('title', "%$search%", 'like');
        $this->productRepository->shouldReceive('findMany')->once()->andReturn(new Collection());

        $result = $this->productService->findMany($sort, $order, $perPage, $page, $search);

        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testValidateOrderItemReturnsTrueOnSuccess(): void
    {
        $data = ['entity_id' => 1];
        $product = Mockery::mock(Product::class);

        $this->productRepository->shouldReceive('findById')->once()->with($data['entity_id'])->andReturn($product);
        $product->shouldReceive('healthCheck')->once()->andReturn(['unhealthy' => ['count' => 0]]);

        $this->assertTrue($this->productService->validateOrderItem($data));
    }

    public function testValidateOrderItemThrowsExceptionIfEntityIdIsEmpty(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Entity ID is required to create an order item');

        $this->productService->validateOrderItem([]);
    }

    public function testValidateOrderItemThrowsExceptionIfProductDoesNotExist(): void
    {
        $data = ['entity_id' => 1];

        $this->productRepository->shouldReceive('findById')->once()->with($data['entity_id'])->andReturn(null);
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Product does not exist');

        $this->productService->validateOrderItem($data);
    }

    public function testValidateOrderItemThrowsProductHealthExceptionIfProductIsUnhealthy(): void
    {
        $data = ['entity_id' => 1];
        $product = Mockery::mock(Product::class);

        $this->productRepository->shouldReceive('findById')->once()->with($data['entity_id'])->andReturn($product);
        $product->shouldReceive('healthCheck')->once()->andReturn(['unhealthy' => ['count' => 1]]);

        $this->expectException(ProductHealthException::class);

        $this->productService->validateOrderItem($data);
    }

    public function testCreateOrderItemCreatesOrderItem(): void
    {
        $order = Order::factory()->create();
        $data = ['entity_id' => 1, 'quantity' => 2];
        $product = Mockery::mock(Product::class);
        $orderItem = new OrderItem();

        $this->productRepository->shouldReceive('findById')->once()->with($data['entity_id'])->andReturn($product);
        $product->shouldReceive('orderItems')->once()->andReturnSelf();
        $product->shouldReceive('create')->once()->with(array_merge($data, ['order_id' => $order->id]))->andReturn($orderItem);

        $result = $this->productService->createOrderItem($order, $data);

        $this->assertInstanceOf(OrderItem::class, $result);
    }

    public function testCreateOrderItemThrowsExceptionIfEntityIdIsEmpty(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Entity ID is required to create an order item');

        $order = Order::factory()->create();
        $this->productService->createOrderItem($order, []);
    }

    public function testCreateOrderItemThrowsExceptionIfProductDoesNotExist(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Product does not exist');

        $order = Order::factory()->create();
        $data = ['entity_id' => 1];

        $this->productRepository->shouldReceive('findById')->once()->with($data['entity_id'])->andReturn(null);

        $this->productService->createOrderItem($order, $data);
    }

    public function testUpdateOrderItemUpdatesOrderItem(): void
    {
        $order = Order::factory()->create();
        $orderItem = OrderItem::factory()->create(['order_itemable_id' => 1]);
        $data = ['quantity' => 3];
        $product = Mockery::mock(Product::class);


        $this->productRepository->shouldReceive('findById')->once()->with($orderItem->order_itemable_id)->andReturn($product);
        $order->items = Mockery::mock();
        $order->items->shouldReceive('where')->once()->with('id', $orderItem->id)->andReturnSelf();
        $order->items->shouldReceive('exists')->once()->andReturn(true);
        $orderItem->shouldReceive('update')->once()->with($data)->andReturn(true);

        $result = $this->productService->updateOrderItem($order, $orderItem, $data);

        $this->assertInstanceOf(OrderItem::class, $result);
    }

    public function testUpdateOrderItemThrowsExceptionIfProductDoesNotExist(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Product does not exist');

        $order = Order::factory()->create();
        $orderItem = OrderItem::factory()->create(['order_itemable_id' => 1]);
        $data = ['quantity' => 3];

        $this->productRepository->shouldReceive('findById')->once()->with($orderItem->order_itemable_id)->andReturn(null);

        $this->productService->updateOrderItem($order, $orderItem, $data);
    }

    public function testUpdateOrderItemThrowsExceptionIfOrderItemDoesNotExistInOrder(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Order item does not exist in the order');

        $order = Order::factory()->create();
        $orderItem = OrderItem::factory()->create(['order_itemable_id' => 1]);
        $data = ['quantity' => 3];
        $product = Mockery::mock(Product::class);

        $this->productRepository->shouldReceive('findById')->once()->with($orderItem->order_itemable_id)->andReturn($product);
        $order->items = Mockery::mock();
        $order->items->shouldReceive('where')->once()->with('id', $orderItem->id)->andReturnSelf();
        $order->items->shouldReceive('exists')->once()->andReturn(false);


        $this->productService->updateOrderItem($order, $orderItem, $data);
    }

    public function testUpdateOrderItemThrowsExceptionIfUpdateFails(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error updating order item for the product');

        $order = Order::factory()->create();
        $orderItem = OrderItem::factory()->create(['order_itemable_id' => 1]);
        $data = ['quantity' => 3];
        $product = Mockery::mock(Product::class);

        $this->productRepository->shouldReceive('findById')->once()->with($orderItem->order_itemable_id)->andReturn($product);
        $order->items = Mockery::mock();
        $order->items->shouldReceive('where')->once()->with('id', $orderItem->id)->andReturnSelf();
        $order->items->shouldReceive('exists')->once()->andReturn(true);
        $orderItem->shouldReceive('update')->once()->with($data)->andReturn(false);


        $this->productService->updateOrderItem($order, $orderItem, $data);
    }

    public function testAttachDiscountRelationsAttachesDiscount(): void
    {
        $discount = Discount::factory()->create();
        $data = ['product_id' => 1, 'price_id' => 1];
        $product = Mockery::mock(Product::class);
        $price = Mockery::mock();

        $this->productRepository->shouldReceive('findById')->once()->with($data['product_id'])->andReturn($product);
        $product->shouldReceive('prices')->once()->andReturnSelf();
        $product->shouldReceive('find')->once()->with($data['price_id'])->andReturn($price);
        $price->shouldReceive('discounts')->once()->andReturnSelf();
        $price->shouldReceive('attach')->once()->with($discount->id);

        $result = $this->productService->attachDiscountRelations($discount, $data);

        $this->assertInstanceOf(Discount::class, $result);
    }

    public function testAttachDiscountRelationsThrowsExceptionIfProductIdIsEmpty(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Product ID is required to create an order item');

        $discount = Discount::factory()->create();
        $this->productService->attachDiscountRelations($discount, ['price_id' => 1]);
    }

    public function testAttachDiscountRelationsThrowsExceptionIfPriceIdIsEmpty(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Price ID is required to create an order item');

        $discount = Discount::factory()->create();
        $this->productService->attachDiscountRelations($discount, ['product_id' => 1]);
    }

    public function testAttachDiscountRelationsThrowsExceptionIfProductDoesNotExist(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Product does not exist');

        $discount = Discount::factory()->create();
        $data = ['product_id' => 1, 'price_id' => 1];

        $this->productRepository->shouldReceive('findById')->once()->with($data['product_id'])->andReturn(null);

        $this->productService->attachDiscountRelations($discount, $data);
    }

    public function testAttachDiscountRelationsThrowsExceptionIfPriceDoesNotExistForProduct(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Price does not exist for the product');

        $discount = Discount::factory()->create();
        $data = ['product_id' => 1, 'price_id' => 1];
        $product = Mockery::mock(Product::class);

        $this->productRepository->shouldReceive('findById')->once()->with($data['product_id'])->andReturn($product);
        $product->shouldReceive('prices')->once()->andReturnSelf();
        $product->shouldReceive('find')->once()->with($data['price_id'])->andReturn(null);

        $this->productService->attachDiscountRelations($discount, $data);
    }
}