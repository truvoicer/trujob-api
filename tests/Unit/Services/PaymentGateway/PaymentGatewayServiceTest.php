<?php

namespace Tests\Unit\Services\PaymentGateway;

use App\Enums\Payment\PaymentGateway as PaymentPaymentGateway;
use App\Models\PaymentGateway;
use App\Services\PaymentGateway\PaymentGatewayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentGatewayServiceTest extends TestCase
{
    use RefreshDatabase;

    protected PaymentGatewayService $paymentGatewayService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentGatewayService = new PaymentGatewayService();
    }

    public function testCreatePaymentGateway(): void
    {
        $data = [
            'name' => 'Test Gateway',
            'label' => 'Test Label',
            'description' => 'Test Description',
            'is_active' => true,
            'is_default' => false,
            'required_fields' => [],
        ];

        $result = $this->paymentGatewayService->createPaymentGateway($data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('payment_gateways', $data);
    }

    public function testCreatePaymentGatewayThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error creating product paymentGateway');

        $data = [
            'name' => null, // Will cause a database error
            'label' => 'Test Label',
            'description' => 'Test Description',
            'is_active' => true,
            'is_default' => false,
            'required_fields' => [],
        ];

        $this->paymentGatewayService->createPaymentGateway($data);
    }

    public function testUpdatePaymentGateway(): void
    {
        $paymentGateway = PaymentGateway::factory()->create();
        $data = [
            'label' => 'Updated Label',
            'description' => 'Updated Description',
        ];

        $result = $this->paymentGatewayService->updatePaymentGateway($paymentGateway, $data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('payment_gateways', [
            'id' => $paymentGateway->id,
            'label' => 'Updated Label',
            'description' => 'Updated Description',
        ]);
    }

    public function testUpdatePaymentGatewayThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error updating product paymentGateway');

        $paymentGateway = PaymentGateway::factory()->create();
        $data = [
            'name' => null, // will throw an error
            'label' => 'Updated Label',
            'description' => 'Updated Description',
        ];

        $this->paymentGatewayService->updatePaymentGateway($paymentGateway, $data);
    }

    public function testDeletePaymentGateway(): void
    {
        $paymentGateway = PaymentGateway::factory()->create();

        $result = $this->paymentGatewayService->deletePaymentGateway($paymentGateway);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('payment_gateways', ['id' => $paymentGateway->id]);
    }

    public function testDeletePaymentGatewayThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error deleting product paymentGateway');

        $paymentGateway = $this->getMockBuilder(PaymentGateway::class)
            ->onlyMethods(['delete'])
            ->getMock();

        $paymentGateway->method('delete')->willReturn(false);

        $this->paymentGatewayService->deletePaymentGateway($paymentGateway);
    }

    public function testSeed(): void
    {
        // Create dummy required-fields.json files
        Storage::fake('local');
        Storage::disk('local')->makeDirectory('app/private/payment-gateway/' . PaymentPaymentGateway::Mollie->value);
        Storage::disk('local')->put('app/private/payment-gateway/' . PaymentPaymentGateway::Mollie->value . '/required-fields.json', json_encode(['api_key']));
        Storage::disk('local')->makeDirectory('app/private/payment-gateway/' . PaymentPaymentGateway::Paypal->value);
        Storage::disk('local')->put('app/private/payment-gateway/' . PaymentPaymentGateway::Paypal->value . '/required-fields.json', json_encode(['client_id', 'client_secret']));


        $this->paymentGatewayService->seed();

        $this->assertDatabaseHas('payment_gateways', [
            'name' => PaymentPaymentGateway::Mollie->value,
            'label' => PaymentPaymentGateway::Mollie->label(),
            'description' => PaymentPaymentGateway::Mollie->description(),
            'is_active' => true,
            'is_default' => PaymentPaymentGateway::Mollie->isDefault(),
        ]);

        $this->assertDatabaseHas('payment_gateways', [
            'name' => PaymentPaymentGateway::Paypal->value,
            'label' => PaymentPaymentGateway::Paypal->label(),
            'description' => PaymentPaymentGateway::Paypal->description(),
            'is_active' => true,
            'is_default' => PaymentPaymentGateway::Paypal->isDefault(),
        ]);
    }

    protected function tearDown(): void
    {
        Storage::fake('local');
        parent::tearDown();
    }
}
