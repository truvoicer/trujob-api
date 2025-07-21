<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Passport\Passport;

class OrderTransactionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $order = Order::factory()->create();
        Transaction::factory(3)->create(['order_id' => $order->id]);

        $response = $this->getJson(route('api.orders.transactions.index', $order));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'amount',
                        'type',
                        // Add other expected fields from TransactionResource
                    ],
                ],
                'links',
                'meta'
            ]);
    }

    public function test_show(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $order = Order::factory()->create();
        $transaction = Transaction::factory()->create(['order_id' => $order->id]);

        $response = $this->getJson(route('api.orders.transactions.show', [$order, $transaction]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'amount',
                    'type',
                    // Add other expected fields from TransactionResource
                ],
            ]);
    }

    public function test_store(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $order = Order::factory()->create();
        $transactionData = [
            'amount' => $this->faker->numberBetween(10, 100),
            'type' => $this->faker->randomElement(['credit', 'debit']),
            'notes' => $this->faker->sentence,
        ];

        $response = $this->postJson(route('api.orders.transactions.store', $order), $transactionData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'id',
                    'amount',
                    'type',
                    // Add other expected fields from TransactionResource
                ],
            ]);

        $this->assertDatabaseHas('transactions', $transactionData + ['order_id' => $order->id]);
    }

    public function test_store_validation_error(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $order = Order::factory()->create();
        $transactionData = [
            'amount' => 'invalid', // Invalid amount
        ];

        $response = $this->postJson(route('api.orders.transactions.store', $order), $transactionData);

        $response->assertStatus(422);
    }

    public function test_update(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $order = Order::factory()->create();
        $transaction = Transaction::factory()->create(['order_id' => $order->id]);

        $updatedData = [
            'amount' => $this->faker->numberBetween(10, 100),
            'type' => $this->faker->randomElement(['credit', 'debit']),
        ];

        $response = $this->putJson(route('api.orders.transactions.update', [$order, $transaction]), $updatedData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Transaction updated']);

        $this->assertDatabaseHas('transactions', $updatedData + ['id' => $transaction->id]);
    }

    public function test_destroy(): void
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $order = Order::factory()->create();
        $transaction = Transaction::factory()->create(['order_id' => $order->id]);

        $response = $this->deleteJson(route('api.orders.transactions.destroy', [$order, $transaction]));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Transaction deleted']);

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    }
}