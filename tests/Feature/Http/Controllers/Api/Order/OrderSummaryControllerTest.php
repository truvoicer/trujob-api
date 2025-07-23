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
use App\Models\PriceSubscription;
use App\Models\PriceSubscriptionItem;
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
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;
use Laravel\Passport\Passport;
use Symfony\Component\HttpFoundation\Response;
use Tests\Helpers\ResponseTestHelpers;

class OrderSummaryControllerTest extends TestCase
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
    public function test_show_one_time_order_summary(): void
    {
        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );

        // Arrange
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

        // Act
        $response = $this
            ->getJson(route('order.summary.show', $order));

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'price_type',
                    'items' => [],
                    "total_price",
                    'total_quantity',
                    'total_tax',
                    'total_discount',
                    'final_total',
                    'total_items',
                    'average_price_per_item',
                    'total_shipping_cost',
                    'total_price_with_shipping',
                    'total_price_after_discounts',
                    'total_price_after_tax',
                    'total_price_after_tax_and_discounts',
                    'default_discounts',
                    'default_tax_rates',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    public function test_show_subscription_order_summary(): void
    {
        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );

        // Arrange
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
                    ->has(
                        PriceSubscriptionItem::factory()
                            ->state([
                                'price_currency_id' => $this->currency->id,
                            ])
                            ->count(3)
                    )
            )
            ->create([
                'price_type' => PriceType::SUBSCRIPTION,
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

        // Act
        $response = $this
            ->getJson(route('order.summary.show', $order));

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'status',
                    'items',
                    'total_price',
                    'total_quantity',
                    'total_tax',
                    'total_discount',
                    'final_total',
                    'total_items',
                    'average_price_per_item',
                    'total_shipping_cost',
                    'total_price_with_shipping',
                    'total_price_after_discounts',
                    'total_price_after_tax',
                    'total_price_after_tax_and_discounts',
                    'default_discounts',
                    'default_tax_rates',
                    'created_at',
                    'updated_at'
                ],
            ])
            ->assertJson(
                fn(AssertableJson $json) =>
                $json->where('data.id', $order->id)
                    ->where('data.price_type', PriceType::SUBSCRIPTION->value)
                    ->etc()
            );
    }

    public function test_show_order_summary_unauthenticated(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs(
            $user,
            ['*']
        );

        // Arrange
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

        // Act
        $response = $this->getJson(route('order.summary.show', $order));

        // Assert
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
