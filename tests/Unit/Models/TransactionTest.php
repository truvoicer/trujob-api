<?php

namespace Tests\Unit\Models;

use App\Models\Currency;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Order;
use App\Models\PaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    protected Transaction $transaction;
    protected function setUp(): void
    {
        parent::setUp();
        $currency = Currency::factory()->create();
        $user = User::factory()->create();
        $paymentGateway = PaymentGateway::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'currency_id' => $currency->id,
        ]);
        $this->transaction = Transaction::factory()->create([
            'order_id' => $order->id,
            'payment_gateway_id' => $paymentGateway->id,
        ]);
    }


    /**
     * Test the order relationship.
     *
     * @return void
     */
    public function testOrderRelationship()
    {
        $this->assertInstanceOf(Order::class, $this->transaction->order);
    }

    /**
     * Test the paymentGateway relationship.
     *
     * @return void
     */
    public function testPaymentGatewayRelationship()
    {
        $this->assertInstanceOf(PaymentGateway::class, $this->transaction->paymentGateway);
    }
}
