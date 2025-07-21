<?php

namespace Tests\Feature\Api\Order\Transaction\PaymentGateway\PayPal;

use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PayPalOrderTransactionCaptureControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    
    public function it_can_capture_a_paypal_order()
    {
        // Arrange
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $transaction = Transaction::factory()->create(['order_id' => $order->id]);
        $order_id = 'some_paypal_order_id';

        // Act
        $response = $this->actingAs($user, 'api')
            ->postJson(route('api.orders.transactions.paypal.capture.store', [$order->id, $transaction->id]), [
                'order_id' => $order_id,
            ]);

        // Assert
        $response->assertStatus(201); // Created
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);
        $response->assertJson([
            'success' => true,
            'message' => 'PayPal order captured',
        ]);
    }

    
    public function it_returns_unprocessable_entity_if_capture_fails()
    {
        // Arrange
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $transaction = Transaction::factory()->create(['order_id' => $order->id]);
        $order_id = 'invalid_paypal_order_id';

        // Mock the PayPalOrderService to return false (capture failed)
        $this->mock(\App\Services\Payment\PayPal\PayPalOrderService::class, function ($mock) {
            $mock->shouldReceive('setUser')->andReturnSelf();
            $mock->shouldReceive('setSite')->andReturnSelf();
            $mock->shouldReceive('captureOrder')->andReturn(false);
        });


        // Act
        $response = $this->actingAs($user, 'api')
            ->postJson(route('api.orders.transactions.paypal.capture.store', [$order->id, $transaction->id]), [
                'order_id' => $order_id,
            ]);

        // Assert
        $response->assertStatus(422); // Unprocessable Entity
        $response->assertJsonStructure([
            'success',
            'message',
            'data',
        ]);
        $response->assertJson([
            'success' => true,
            'message' => 'Error capturing PayPal order',
            'data' => null
        ]);
    }

    
    public function it_validates_the_store_request()
    {
        // Arrange
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $transaction = Transaction::factory()->create(['order_id' => $order->id]);

        // Act
        $response = $this->actingAs($user, 'api')
            ->postJson(route('api.orders.transactions.paypal.capture.store', [$order->id, $transaction->id]), []);

        // Assert
        $response->assertStatus(422); // Unprocessable Entity
        $response->assertJsonValidationErrors(['order_id']);
    }
}