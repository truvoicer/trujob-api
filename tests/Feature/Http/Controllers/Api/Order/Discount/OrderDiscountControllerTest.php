<?php

namespace Tests\Feature;

use App\Enums\Order\OrderItemType;
use App\Enums\Payment\PaymentGateway as PaymentPaymentGateway;
use App\Enums\Price\PriceType;
use App\Models\Discount;
use App\Models\Order;
use App\Enums\SiteStatus;
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

class OrderDiscountControllerTest extends TestCase
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
    public function test_index_returns_discounts_for_order()
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

        $discount = Discount::factory(3)->create([
            'currency_id' => $this->currency->id,
        ]);
        $order->discounts()->attach($discount->pluck('id'));
        $response = $this
            ->getJson(route('order.discount.index', $order));

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_store_attaches_discount_to_order()
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
        $discount = Discount::factory()->create([
            'currency_id' => $this->currency->id,
        ]);;

        $response = $this
            ->postJson(route('order.discount.store', [$order, $discount]));

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Order discount created']);
        $this->assertDatabaseHas('discount_orders', [
            'order_id' => $order->id,
            'discount_id' => $discount->id,
        ]);
    }

    public function test_store_returns_error_if_discount_already_exists_in_order()
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
        $discount = Discount::factory()->create([
            'currency_id' => $this->currency->id,
        ]);;
        $order->discounts()->attach($discount->id);

        $response = $this
            ->postJson(route('order.discount.store', [$order, $discount]));

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Discount already exists in order']);
    }

    public function test_destroy_detaches_discount_from_order()
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
        $discount = Discount::factory()->create([
            'currency_id' => $this->currency->id,
        ]);;
        $order->discounts()->attach($discount->id);

        $response = $this
            ->deleteJson(route('order.discount.destroy', [$order, $discount]));

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Order discount deleted']);
        $this->assertDatabaseMissing('discount_orders', [
            'order_id' => $order->id,
            'discount_id' => $discount->id,
        ]);
    }
}