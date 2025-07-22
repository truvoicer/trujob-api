<?php

namespace Tests\Feature;

use App\Enums\Order\OrderItemType;
use App\Enums\Order\OrderStatus;
use App\Enums\Price\PriceType;
use App\Models\Order;
use App\Models\Product;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use App\Enums\SiteStatus;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Price;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use Laravel\Sanctum\Sanctum;

class UserOrderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
        $this->user->roles()->attach(Role::factory()->create(['name' => 'superuser'])->id);

        $this->siteUser = SiteUser::create([
            'user_id' => $this->user->id,
            'site_id' => $this->site->id,
            'status' => SiteStatus::ACTIVE->value,
        ]);
        Sanctum::actingAs($this->siteUser, ['*']);

        $this->currency = Currency::factory()->create();
        $this->country = Country::factory()->create();
        $this->user->userSetting()->create([
            'currency_id' => $this->currency->id,
            'country_id' => $this->country->id,
        ]);
    }

    public function test_index(): void
    {
        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );

        $currency = Currency::factory()->create();
        $country = Country::factory()->create();
        
        $orders = Order::factory(3)->create([
            'user_id' => $this->user->id,
            'currency_id' => $currency->id,
            'country_id' => $country->id,
        ]);

        $response = $this->getJson(route('order.index', ['product' => 1]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links',
            'meta',
        ]);
    }

    public function test_show(): void
    {
        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );

        $product = Product::factory()->create([
            'user_id' => $this->user->id,
        ]);
        $currency = Currency::factory()->create();
        $country = Country::factory()->create();
        
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $currency->id,
            'country_id' => $country->id,
        ]);

        $response = $this->getJson(route('order.show', ['order' => $order->id]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function test_show_not_found(): void
    {
        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );

        $product = Product::factory()->create([
            'user_id' => $this->user->id,
        ]);
        $currency = Currency::factory()->create();
        $country = Country::factory()->create();
        
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $currency->id,
            'country_id' => $country->id,
        ]);


        $response = $this->getJson(route('order.show', ['order' => 2]));

        $response->assertStatus(404);
    }

    public function test_store(): void
    {
        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );

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

        $data = [
            'price_type' => PriceType::ONE_TIME->value,
            'items' => [
                [
                    'entity_type' => OrderItemType::PRODUCT->value,
                    'entity_id' => $product->id,
                    'quantity' => 1,
                ],
            ]
        ];

        $response = $this->postJson(route('order.store', ['product' => $product->id]), $data);

        $response->assertStatus(201);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'status',
            ],
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
        ]);
    }

    public function test_store_validation_error(): void
    {
        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
        ]);

        $data = []; // Empty data to trigger validation errors

        $response = $this->postJson(route('order.store', ['product' => $product->id]), $data);

        $response->assertStatus(422);
    }

    public function test_update(): void
    {
        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
        ]);
        $currency = Currency::factory()->create();
        $country = Country::factory()->create();
        
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $currency->id,
            'country_id' => $country->id,
        ]);

        $data = [
            'status' => OrderStatus::COMPLETED->value,
        ];

        $response = $this->patchJson(route('order.update', ['order' => $order->id]), $data);
        // dd($response->json());
        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Order order updated',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
        ]);
    }

    public function test_destroy(): void
    {
        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );
        $product = Product::factory()->create([
            'user_id' => $this->user->id,
        ]);
        $currency = Currency::factory()->create();
        $country = Country::factory()->create();
        
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $currency->id,
            'country_id' => $country->id,
        ]);

        $response = $this->deleteJson(route('order.destroy', ['order' => $order->id]));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Order order deleted',
        ]);

        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
        ]);
    }
}
