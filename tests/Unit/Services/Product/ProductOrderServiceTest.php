<?php

namespace Tests\Unit\Services\Product;

use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Services\Product\ProductOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductOrderServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductOrderService $productOrderService;
    protected User $user;
    protected Product $product;
    protected Order $order;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->actingAs($this->user); // Authenticate the user
        $this->product = Product::factory()->create();
        $this->order = Order::factory()->create(['user_id' => $this->user->id]);


        $this->productOrderService = new ProductOrderService();
        $this->productOrderService->setUser($this->user);

    }

    public function testCreateProductOrder(): void
    {
        $data = ['total' => 100, 'status' => 'pending'];

        $result = $this->productOrderService->createProductOrder($this->product, $data);

        $this->assertTrue($result);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'total' => 100,
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('order_product', [
            'product_id' => $this->product->id,
            'order_id' => Order::latest()->first()->id, // Verify the latest created order
        ]);
    }

    public function testUpdateProductOrder(): void
    {
        // Attach the order to the product first, otherwise update will fail
        $this->product->orders()->attach($this->order->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $data = ['total' => 200, 'status' => 'completed'];

        $result = $this->productOrderService->updateProductOrder($this->product, $this->order, $data);

        $this->assertTrue($result);

        $this->assertDatabaseHas('orders', [
            'id' => $this->order->id,
            'total' => 200,
            'status' => 'completed',
        ]);
    }

    public function testUpdateProductOrderThrowsExceptionWhenOrderNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Order not found in product');

        $order = Order::factory()->create(['user_id' => $this->user->id]); // Create a NEW order that is not linked to the product

        $this->productOrderService->updateProductOrder($this->product, $order, ['total' => 200]);
    }

    public function testDeleteProductOrder(): void
    {
        // Attach the order to the product first
        $this->product->orders()->attach($this->order->id, [
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $result = $this->productOrderService->deleteProductOrder($this->product, $this->order);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('orders', ['id' => $this->order->id]);
        $this->assertDatabaseMissing('order_product', ['product_id' => $this->product->id, 'order_id' => $this->order->id]);
    }

    public function testDeleteProductOrderThrowsExceptionWhenOrderNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Order not found in product');

        $order = Order::factory()->create(['user_id' => $this->user->id]); // Create a NEW order that is not linked to the product
        $this->productOrderService->deleteProductOrder($this->product, $order);
    }
}
