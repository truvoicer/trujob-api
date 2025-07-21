<?php

namespace Tests\Unit\Models;

use App\Models\PaymentGateway;
use App\Models\PaymentMethod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentMethodTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the paymentGateways relationship.
     *
     * @return void
     */
    public function testPaymentGatewaysRelationship(): void
    {
        // Arrange
        $paymentMethod = PaymentMethod::factory()->create();
        $paymentGateway = PaymentGateway::factory()->create();

        // Attach the payment gateway to the payment method
        $paymentMethod->paymentGateways()->attach($paymentGateway->id);

        // Act
        $relatedPaymentGateways = $paymentMethod->paymentGateways;

        // Assert
        $this->assertInstanceOf(Collection::class, $relatedPaymentGateways);
        $this->assertCount(1, $relatedPaymentGateways);
        $this->assertInstanceOf(PaymentGateway::class, $relatedPaymentGateways->first());
        $this->assertEquals($paymentGateway->id, $relatedPaymentGateways->first()->id);
    }
}
