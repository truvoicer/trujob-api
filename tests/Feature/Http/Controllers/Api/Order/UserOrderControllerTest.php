<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Sanctum\Sanctum;

class UserOrderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
            ['*']
        );

        $orders = Order::factory(3)->create(['user_id' => $user->id]);

        $response = $this->getJson(route('orders.index', ['product' => 1]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links',
            'meta',
        ]);
    }

    public function test_show(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
            ['*']
        );

        $product = Product::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $product->orders()->attach($order);

        $response = $this->getJson(route('orders.show', ['product' => $product->id, 'order' => $order->id]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_show_not_found(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
            ['*']
        );

        $product = Product::factory()->create();
        $order = Order::factory()->create();

        $response = $this->getJson(route('orders.show', ['product' => $product->id, 'order' => $order->id]));

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Order not found in product',
        ]);
    }

    public function test_store(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
            ['*']
        );
        $product = Product::factory()->create();

        $data = [
            'total' => $this->faker->randomFloat(2, 10, 100),
            'address' => $this->faker->address,
        ];

        $response = $this->postJson(route('orders.store', ['product' => $product->id]), $data);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'Order order created',
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
        ]);
    }

    public function test_store_validation_error(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
            ['*']
        );
        $product = Product::factory()->create();

        $data = []; // Empty data to trigger validation errors

        $response = $this->postJson(route('orders.store', ['product' => $product->id]), $data);

        $response->assertStatus(422);
    }

    public function test_update(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
            ['*']
        );
        $product = Product::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $product->orders()->attach($order);


        $data = [
            'total' => $this->faker->randomFloat(2, 10, 100),
            'address' => $this->faker->address,
        ];

        $response = $this->putJson(route('orders.update', ['product' => $product->id, 'order' => $order->id]), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Order order updated',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
        ]);
    }

    public function test_destroy(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
            ['*']
        );
        $product = Product::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);
        $product->orders()->attach($order);

        $response = $this->deleteJson(route('orders.destroy', ['product' => $product->id, 'order' => $order->id]));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Order order deleted',
        ]);

        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
        ]);
    }
}