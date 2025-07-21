<?php

namespace Tests\Feature;

use App\Enums\Price\PriceType;
use App\Models\Order;
use App\Models\Site;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PayPalOrderTransactionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_can_show_a_paypal_order()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create(['site_id' => $site->id]);
        $transaction = Transaction::factory()->create(['order_id' => $order->id]);

        $response = $this->actingAs($user)
            ->getJson(route('api.orders.transactions.payment-gateway.paypal.show', ['order' => $order->id, 'transaction' => $transaction->id]));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Order retrieved successfully',
            ]);
    }

    /** @test */
    public function it_can_store_a_paypal_one_time_order()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create(['site_id' => $site->id, 'price_type' => PriceType::ONE_TIME]);
        $transaction = Transaction::factory()->create(['order_id' => $order->id]);

        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $response = $this->actingAs($user)
            ->postJson(route('api.orders.transactions.payment-gateway.paypal.store', ['order' => $order->id, 'transaction' => $transaction->id]), $data);

        $response->assertStatus(201);
    }

    /** @test */
    public function it_can_store_a_paypal_subscription_order()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create(['site_id' => $site->id, 'price_type' => PriceType::SUBSCRIPTION]);
        $transaction = Transaction::factory()->create(['order_id' => $order->id]);

        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $response = $this->actingAs($user)
            ->postJson(route('api.orders.transactions.payment-gateway.paypal.store', ['order' => $order->id, 'transaction' => $transaction->id]), $data);

        $response->assertStatus(201);
    }

    /** @test */
    public function it_returns_unprocessable_entity_for_invalid_price_type()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create(['site_id' => $site->id, 'price_type' => 'invalid_type']);
        $transaction = Transaction::factory()->create(['order_id' => $order->id]);

        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $response = $this->actingAs($user)
            ->postJson(route('api.orders.transactions.payment-gateway.paypal.store', ['order' => $order->id, 'transaction' => $transaction->id]), $data);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid price type',
            ]);
    }

    /** @test */
    public function it_can_update_a_paypal_order()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create(['site_id' => $site->id]);
        $transaction = Transaction::factory()->create(['order_id' => $order->id]);

        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $response = $this->actingAs($user)
            ->putJson(route('api.orders.transactions.payment-gateway.paypal.update', ['order' => $order->id, 'transaction' => $transaction->id]), $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'PaymentGateway updated',
            ]);
    }

    /** @test */
    public function it_can_destroy_a_paypal_order()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create(['site_id' => $site->id]);
        $transaction = Transaction::factory()->create(['order_id' => $order->id]);

        $response = $this->actingAs($user)
            ->deleteJson(route('api.orders.transactions.payment-gateway.paypal.destroy', ['order' => $order->id, 'transaction' => $transaction->id]));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'PaymentGateway deleted',
            ]);
    }
}