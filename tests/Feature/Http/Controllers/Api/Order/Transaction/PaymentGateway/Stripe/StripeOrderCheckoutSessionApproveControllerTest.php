<?php

namespace Tests\Feature\Api\Order\Transaction\PaymentGateway\Stripe;

use App\Enums\Order\OrderItemType;
use App\Enums\Payment\PaymentGateway as PaymentPaymentGateway;
use App\Enums\Price\PriceType;
use App\Models\Order;

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
use Laravel\Sanctum\Sanctum;
use App\Models\Transaction;
use App\Services\JWT\JWTService;
use App\Services\Payment\Stripe\StripeOrderService;
use App\Services\Payment\Stripe\StripeSubscriptionOrderService;
use Database\Seeders\payment\PaymentGatewaySeeder;
use Database\Seeders\payment\SitePaymentGatewaySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Tests\Helpers\ResponseTestHelpers;
use Tests\TestCase;

class StripeOrderCheckoutSessionApproveControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected PaymentGateway $paymentGateway;
    protected SiteUser $siteUser;
    protected Site $site;
    protected User $user;
    protected Currency $currency;
    protected Country $country;

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

    #[DataProvider('checkoutTypeProvider')]
    public function test_checkout_session_approve_request_success(string $checkoutType): void
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


        $response = $this
            ->postJson(
                route(
                    'order.transaction.payment-gateway.stripe.checkout-session.store',
                    [
                        'order' => $order->id,
                        'transaction' => $transaction->id
                    ]
                ),
                [
                    'checkout_type' => $checkoutType,
                ]
            );

        $response->assertStatus(Response::HTTP_CREATED);
        ResponseTestHelpers::assertEncryptedResponse($response);

        $payload = ResponseTestHelpers::extractEncryptedResponseData($response);
        $this->assertArrayHasKey('data', $payload);
        $this->assertArrayHasKey('id', $payload['data']);

        // Act
        $response = $this
            ->postJson(
                route(
                    'order.transaction.payment-gateway.stripe.checkout-session.approve.store',
                    [
                        'order' => $order->id,
                        'transaction' => $transaction->id
                    ]
                ),
                [
                    'id' => $payload['data']['id'], // Mocked session ID
                ]
            );
        $response->assertStatus(Response::HTTP_CREATED);
        ResponseTestHelpers::assertEncryptedResponse($response);

        $payload = ResponseTestHelpers::extractEncryptedResponseData($response);
        
        $this->assertArrayHasKey('data', $payload);
        $data = $payload['data'];
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('payment_status', $data);

        $this->assertEquals($data['status'], 'open');
        $this->assertEquals($data['payment_status'], 'unpaid');
    }

    public static function checkoutTypeProvider(): array
    {
        return [
            ['one_time'],
            ['subscription'],
        ];
    }
}
