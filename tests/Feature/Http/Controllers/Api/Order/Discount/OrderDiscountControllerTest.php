<?php

namespace Tests\Feature;

use App\Models\Discount;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderDiscountControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index_returns_discounts_for_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $discounts = Discount::factory(3)->create();
        $order->discounts()->attach($discounts->pluck('id')->toArray());

        $response = $this->actingAs($user)
            ->getJson(route('orders.discounts.index', $order));

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_store_attaches_discount_to_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $discount = Discount::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('orders.discounts.store', [$order, $discount]));

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Order discount created']);
        $this->assertDatabaseHas('discount_order', [
            'order_id' => $order->id,
            'discount_id' => $discount->id,
        ]);
    }

    public function test_store_returns_error_if_discount_already_exists_in_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $discount = Discount::factory()->create();
        $order->discounts()->attach($discount->id);

        $response = $this->actingAs($user)
            ->postJson(route('orders.discounts.store', [$order, $discount]));

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Discount already exists in order']);
    }

    public function test_destroy_detaches_discount_from_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $discount = Discount::factory()->create();
        $order->discounts()->attach($discount->id);

        $response = $this->actingAs($user)
            ->deleteJson(route('orders.discounts.destroy', [$order, $discount]));

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Order discount deleted']);
        $this->assertDatabaseMissing('discount_order', [
            'order_id' => $order->id,
            'discount_id' => $discount->id,
        ]);
    }
}