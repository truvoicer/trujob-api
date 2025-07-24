<?php

namespace Tests\Feature;

use App\Enums\Order\OrderItemType;
use App\Enums\Payment\PaymentGateway as PaymentPaymentGateway;
use App\Enums\Price\PriceType;
use App\Models\Order;

use App\Enums\SiteStatus;
use App\Enums\Subscription\SubscriptionTenureType;
use App\Enums\Transaction\TransactionPaymentStatus;
use App\Enums\Transaction\TransactionStatus;
use App\Models\Address;
use App\Models\Country;
use App\Models\Currency;
use App\Models\PaymentGateway;
use App\Models\Price;
use App\Models\PriceSubscription;
use App\Models\PriceSubscriptionItem;
use App\Models\Product;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use Laravel\Sanctum\Sanctum;
use App\Models\Transaction;
use Database\Seeders\payment\PaymentGatewaySeeder;
use Database\Seeders\payment\SitePaymentGatewaySeeder;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Helpers\ResponseTestHelpers;
use Tests\TestCase;

class PayPalOrderTransactionControllerTest extends TestCase
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

    public function test_it_can_store_a_paypal_one_time_order()
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
        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $response = $this
            ->postJson(route('order.transaction.payment-gateway.paypal.store', ['order' => $order->id, 'transaction' => $transaction->id]), $data);

        $response->assertStatus(201);
        ResponseTestHelpers::assertEncryptedResponse($response);
        $payload = ResponseTestHelpers::extractEncryptedResponseData($response);

        $this->assertArrayHasKey('message', $payload);
        $this->assertEquals('PayPal order created', $payload['message']);
        $this->assertArrayHasKey('data', $payload);
        $payloadData = $payload['data'];
        $this->assertArrayHasKey('id', $payloadData);
        $this->assertArrayHasKey('links', $payloadData);
        $this->assertEquals($payloadData['status'], 'CREATED');
    }


    public function test_it_can_store_a_paypal_subscription_order()
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
            'price_type' => PriceType::SUBSCRIPTION,
            'user_id' => $this->user->id,
            'country_id' => $this->country->id,
            'currency_id' => $this->currency->id,
            'billing_address_id' => $billingAddress->id,
            'shipping_address_id' => $shippingAddress->id,
        ]);

        $price = Price::factory()
            ->has(
                PriceSubscription::factory()
                    ->state([
                        'setup_fee_currency_id' => $this->currency->id,
                    ])
                    ->has(
                        PriceSubscriptionItem::factory()
                            ->state(new Sequence(
                                fn(Sequence $sequence) => [
                                    'price_currency_id' => $this->currency->id,
                                    'sequence' => $sequence->index + 1,
                                    'tenure_type' => ($sequence->index === 0) 
                                    ? SubscriptionTenureType::TRIAL->value
                                    : SubscriptionTenureType::REGULAR->value,
                                ],
                            ))
                            ->count(2)
                    )
            )
            ->create([
                'price_type' => PriceType::SUBSCRIPTION->value,
                'created_by_user_id' => $this->user->id,
                'currency_id' => $this->currency->id,
                'country_id' => $this->country->id,
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
        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $response = $this
            ->postJson(route('order.transaction.payment-gateway.paypal.store', ['order' => $order->id, 'transaction' => $transaction->id]), $data);

        $response->assertStatus(201);

        ResponseTestHelpers::assertEncryptedResponse($response);
        $payload = ResponseTestHelpers::extractEncryptedResponseData($response);
        
        $this->assertArrayHasKey('message', $payload);
        $this->assertEquals('PayPal subscription created', $payload['message']);
        $this->assertArrayHasKey('data', $payload);
        $payloadData = $payload['data'];
        $this->assertArrayHasKey('id', $payloadData);
        $this->assertArrayHasKey('links', $payloadData);
        $this->assertEquals($payloadData['status'], 'APPROVAL_PENDING');
    }

}
