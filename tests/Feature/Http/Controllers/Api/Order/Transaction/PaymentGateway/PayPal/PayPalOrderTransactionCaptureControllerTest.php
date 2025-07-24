<?php

namespace Tests\Feature\Api\Order\Transaction\PaymentGateway\PayPal;

use App\Enums\Order\OrderItemType;
use App\Enums\Payment\PaymentGateway as PaymentPaymentGateway;
use App\Enums\Price\PriceType;
use App\Models\Order;
use App\Models\Transaction;

use App\Enums\SiteStatus;
use App\Enums\Transaction\TransactionPaymentStatus;
use App\Enums\Transaction\TransactionStatus;
use App\Models\Address;
use App\Models\Country;
use App\Models\Currency;
use App\Models\PaymentGateway;
use App\Models\Price;
use App\Models\Product;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use Database\Seeders\payment\PaymentGatewaySeeder;
use Database\Seeders\payment\SitePaymentGatewaySeeder;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\ResponseTestHelpers;
use Tests\TestCase;

class PayPalOrderTransactionCaptureControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected SiteUser $siteUser;
    protected Site $site;
    protected User $user;
    protected Currency $currency;
    protected Country $country;
    protected PaymentGateway $paymentGateway;

    protected function setUp(): void
    {
        parent::setUp();
        // Additional setup if needed
        $this->site = Site::factory()->create();
        $this->user = User::factory()->create();
        $this->user->roles()->attach(Role::factory()->create(['name' => 'superuser'])->id);
        $this->siteUser = SiteUser::create([
            'user_id' => $this->user->id,
            'site_id' => $this->site->id,
            'status' => SiteStatus::ACTIVE->value,
        ]);

        $this->currency = Currency::factory()->create([
            'code' => 'GBP',
            'name' => 'British Pound',
            'symbol' => '£',
            'is_active' => true,
        ]);
        $this->country = Country::factory()->create([
            'name' => 'United Kingdom',
            'iso2' => 'GB',
            'iso3' => 'GBR',
            'is_active' => true,
        ]);
        $this->user->userSetting()->create([
            'currency_id' => $this->currency->id,
            'country_id' => $this->country->id,
        ]);
        $this->site->settings()->create([
            'currency_id' => $this->currency->id,
            'country_id' => $this->country->id,
        ]);
        $this->seed([
            PaymentGatewaySeeder::class,
            SitePaymentGatewaySeeder::class
        ]);
        $this->paymentGateway = PaymentGateway::where(
            'name',
            PaymentPaymentGateway::PAYPAL->value
        )->first();
    }

    // public function test_it_can_capture_a_paypal_order()
    // {
    //     // Arrange
    //     Sanctum::actingAs($this->siteUser, ['*']);

    //     $billingAddress = Address::factory()->create([
    //         'user_id' => $this->user->id,
    //         'country_id' => $this->country->id,
    //     ]);
    //     $shippingAddress = Address::factory()->create([
    //         'user_id' => $this->user->id,
    //         'country_id' => $this->country->id,
    //     ]);
    //     $order = Order::factory()->create([
    //         'price_type' => PriceType::ONE_TIME,
    //         'user_id' => $this->user->id,
    //         'country_id' => $this->country->id,
    //         'currency_id' => $this->currency->id,
    //         'billing_address_id' => $billingAddress->id,
    //         'shipping_address_id' => $shippingAddress->id,
    //     ]);

    //     $price = Price::factory()->create([
    //         'price_type' => PriceType::ONE_TIME->value,
    //         'created_by_user_id' => $this->user->id,
    //         'currency_id' => $this->currency->id,
    //         'country_id' => $this->country->id,
    //     ]);
    //     $product = Product::factory()->create([
    //         'user_id' => $this->user->id,
    //         'sku' => $this->faker->unique()->word,
    //         'active' => true,
    //     ]);
    //     $product->prices()->attach($price->id);
    //     $product->orderItems()->create([
    //         'order_id' => $order->id,
    //         'entity_type' => OrderItemType::PRODUCT->value,
    //         'entity_id' => $product->id,
    //         'quantity' => 1,
    //     ]);
    //     $transaction = Transaction::factory()->create([
    //         'order_id' => $order->id,
    //         'status' => TransactionStatus::PENDING->value,
    //         'payment_status' => TransactionPaymentStatus::UNPAID->value,
    //         'payment_gateway_id' => $this->paymentGateway->id,
    //     ]);

    //     $response = $this
    //         ->postJson(route('order.transaction.payment-gateway.paypal.store', ['order' => $order->id, 'transaction' => $transaction->id]), []);

    //     $response->assertStatus(201);
    //     ResponseTestHelpers::assertEncryptedResponse($response);
    //     $payload = ResponseTestHelpers::extractEncryptedResponseData($response);

    //     $this->assertArrayHasKey('message', $payload);
    //     $this->assertEquals('PayPal order created', $payload['message']);
    //     $this->assertArrayHasKey('data', $payload);
    //     $payloadData = $payload['data'];
    //     $this->assertArrayHasKey('id', $payloadData);
    //     $this->assertArrayHasKey('links', $payloadData);
    //     $this->assertEquals($payloadData['status'], 'CREATED');

    //     // Act
    //     $response = $this
    //         ->postJson(route('order.transaction.payment-gateway.paypal.capture.store', [$order->id, $transaction->id]), [
    //             'order_id' => $payloadData['id'],
    //         ]);

    //         dd($response->getContent());
    //     // Assert
    //     $response->assertStatus(201); // Created
    //     $response->assertJsonStructure([
    //         'success',
    //         'message',
    //         'data',
    //     ]);
    //     $response->assertJson([
    //         'success' => true,
    //         'message' => 'PayPal order captured',
    //     ]);
    // }


    public function test_it_returns_unprocessable_entity_if_capture_fails()
    {
        // Arrange
        Sanctum::actingAs($this->siteUser, ['*']);
        $billingAddress = Address::factory()->create([
            'user_id' => $this->user->id,
            'country_id' => $this->country->id,
        ]);
        $shippingAddress = Address::factory()->create([
            'user_id' => $this->user->id,
            'country_id' => $this->country->id,
        ]);
        $order = Order::factory()->create([
            'price_type' => PriceType::ONE_TIME,
            'user_id' => $this->user->id,
            'country_id' => $this->country->id,
            'currency_id' => $this->currency->id,
            'billing_address_id' => $billingAddress->id,
            'shipping_address_id' => $shippingAddress->id,
        ]);

        $price = Price::factory()->create([
            'price_type' => PriceType::ONE_TIME->value,
            'created_by_user_id' => $this->user->id,
            'currency_id' => $this->currency->id,
            'country_id' => $this->country->id,
            'amount' => 0,
        ]);
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
            'sku' => $this->faker->unique()->word,
            'active' => true,
        ]);
        $product->prices()->attach($price->id);
        $product->orderItems()->create([
            'order_id' => $order->id,
            'entity_type' => OrderItemType::PRODUCT->value,
            'entity_id' => $product->id,
            'quantity' => 1,
        ]);
        $transaction = Transaction::factory()->create([
            'order_id' => $order->id,
            'status' => TransactionStatus::PENDING->value,
            'payment_status' => TransactionPaymentStatus::UNPAID->value,
            'payment_gateway_id' => $this->paymentGateway->id,
        ]);

        // Mock the PayPalOrderService to return false (capture failed)
        $this->mock(\App\Services\Payment\PayPal\PayPalOrderService::class, function ($mock) {
            $mock->shouldReceive('setUser')->andReturnSelf();
            $mock->shouldReceive('setSite')->andReturnSelf();
            $mock->shouldReceive('captureOrder')->andReturn(false);
        });

        $paypalOrderId = 'PAYPAL_ORDER_ID';

        // Act
        $response = $this
            ->postJson(route('order.transaction.payment-gateway.paypal.capture.store', [$order->id, $transaction->id]), [
                'order_id' => $paypalOrderId,
            ]);
        // Assert
        $response->assertStatus(422); // Unprocessable Entity
        ResponseTestHelpers::assertEncryptedResponse($response);
        $payload = ResponseTestHelpers::extractEncryptedResponseData($response);
        $this->assertArrayHasKey('message', $payload);
    }


    public function test_it_validates_the_store_request()
    {
        // Arrange
        Sanctum::actingAs($this->siteUser, ['*']);
        $billingAddress = Address::factory()->create([
            'user_id' => $this->user->id,
            'country_id' => $this->country->id,
        ]);
        $shippingAddress = Address::factory()->create([
            'user_id' => $this->user->id,
            'country_id' => $this->country->id,
        ]);
        $order = Order::factory()->create([
            'price_type' => PriceType::ONE_TIME,
            'user_id' => $this->user->id,
            'country_id' => $this->country->id,
            'currency_id' => $this->currency->id,
            'billing_address_id' => $billingAddress->id,
            'shipping_address_id' => $shippingAddress->id,
        ]);

        $price = Price::factory()->create([
            'price_type' => PriceType::ONE_TIME->value,
            'created_by_user_id' => $this->user->id,
            'currency_id' => $this->currency->id,
            'country_id' => $this->country->id,
            'amount' => 0,
        ]);
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
            'sku' => $this->faker->unique()->word,
            'active' => true,
        ]);
        $product->prices()->attach($price->id);
        $product->orderItems()->create([
            'order_id' => $order->id,
            'entity_type' => OrderItemType::PRODUCT->value,
            'entity_id' => $product->id,
            'quantity' => 1,
        ]);
        $transaction = Transaction::factory()->create([
            'order_id' => $order->id,
            'status' => TransactionStatus::PENDING->value,
            'payment_status' => TransactionPaymentStatus::UNPAID->value,
            'payment_gateway_id' => $this->paymentGateway->id,
        ]);

        // Act
        $response = $this
            ->postJson(route('order.transaction.payment-gateway.paypal.capture.store', [$order->id, $transaction->id]), []);

        // Assert
        $response->assertStatus(422); // Unprocessable Entity
        $response->assertJsonValidationErrors(['order_id']);
    }
}
