<?php

namespace Tests\Unit\Models;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Order;
use App\Models\PaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the user relationship.
     *
     * @return void
     */
    public function testUserRelationship()
    {
        $transaction = Transaction::factory()->create();
        $this->assertInstanceOf(User::class, $transaction->user);
    }

    /**
     * Test the order relationship.
     *
     * @return void
     */
    public function testOrderRelationship()
    {
        $transaction = Transaction::factory()->create();
        $this->assertInstanceOf(Order::class, $transaction->order);
    }

    /**
     * Test the paymentGateway relationship.
     *
     * @return void
     */
    public function testPaymentGatewayRelationship()
    {
        $transaction = Transaction::factory()->create();
        $this->assertInstanceOf(PaymentGateway::class, $transaction->paymentGateway);
    }
}