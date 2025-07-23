<?php

namespace Tests\Feature\Http\Controllers\Api\Order\Transaction;

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
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use Database\Seeders\payment\PaymentGatewaySeeder;
use Database\Seeders\payment\SitePaymentGatewaySeeder;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Tests\Helpers\ResponseTestHelpers;

class OrderTransactionControllerTest extends TestCase
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
        $this->user->roles()->attach(Role::factory()->create([
            'name' => 'superuser'
        ])->id);

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
            PaymentPaymentGateway::STRIPE->value
        )->first();
    }
    public function test_index(): void
    {

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

        $response = $this->getJson(route('order.transaction.index', $order));

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

        $response = $this->getJson(route('order.transaction.show', [$order, $transaction]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'payment_gateway',
                    'currency_code',
                    'amount',
                    'status',
                    'payment_status',
                    'order_data',
                    'transaction_data',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_store(): void
    {
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
        
        $transactionData = [
            'payment_gateway_id' => $this->paymentGateway->id,
        ];

        $response = $this->postJson(route('order.transaction.store', $order), $transactionData);
        ResponseTestHelpers::assertEncryptedResponse($response);
      
        $payload = ResponseTestHelpers::extractEncryptedResponseData($response);
        $this->assertArrayHasKey('data', $payload);

        $this->assertArrayHasKey('id', $payload['data']);
        $this->assertArrayHasKey('payment_gateway', $payload['data']);
        $this->assertArrayHasKey('currency_code', $payload['data']);
        $this->assertArrayHasKey('amount', $payload['data']);
        $this->assertArrayHasKey('status', $payload['data']);
        $this->assertArrayHasKey('payment_status', $payload['data']);
        $this->assertArrayHasKey('order_data', $payload['data']);
        $this->assertArrayHasKey('transaction_data', $payload['data']); 
        $this->assertArrayHasKey('created_at', $payload['data']);
        $this->assertArrayHasKey('updated_at', $payload['data']);

        $this->assertDatabaseHas('transactions', $transactionData + ['order_id' => $order->id]);
    }

    public function test_store_validation_error(): void
    {
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
        $transactionData = [
            'amount' => 'invalid', // Invalid amount
        ];

        $response = $this->postJson(route('order.transaction.store', $order), $transactionData);

        $response->assertStatus(422);
    }

    public function test_update(): void
    {
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

        $updatedData = [
            'amount' => $this->faker->numberBetween(10, 100),

        ];

        $response = $this->patchJson(route('order.transaction.update', [$order, $transaction]), $updatedData);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Transaction updated']);

        $this->assertDatabaseHas('transactions', $updatedData + ['id' => $transaction->id]);
    }

    public function test_destroy(): void
    {
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
        $response = $this->deleteJson(route('order.transaction.destroy', [$order, $transaction]));

        $response->assertStatus(200)
            ->assertJson(['message' => 'Transaction deleted']);

        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    }
}