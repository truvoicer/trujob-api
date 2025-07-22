<?php

namespace Tests\Feature\Api\Order;

use App\Enums\Price\PriceType;
use App\Models\Order;

use App\Enums\SiteStatus;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class OrderSummaryControllerTest extends TestCase
{
    use RefreshDatabase;

    protected SiteUser $siteUser;
    protected Site $site;
    protected User $user;
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
    }
    public function test_show_one_time_order_summary(): void
    {
        // Arrange
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $user->sites()->attach($site);
        $order = Order::factory()->create([
            'price_type' => PriceType::ONE_TIME,
            'site_id' => $site->id,
        ]);

        // Act
        $response = $this
            ->getJson(route('api.order-summary.show', $order));

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'price_type',
                    'items' => [],
                ],
            ])
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.id', $order->id)
                     ->where('data.price_type', PriceType::ONE_TIME->value)
                     ->etc()
            );
    }

    public function test_show_subscription_order_summary(): void
    {
        // Arrange
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $user->sites()->attach($site);
        $order = Order::factory()->create([
            'price_type' => PriceType::SUBSCRIPTION,
            'site_id' => $site->id,
        ]);

        // Act
        $response = $this
            ->getJson(route('api.order-summary.show', $order));

        // Assert
        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'price_type',
                    'items' => [],
                ],
            ])
            ->assertJson(fn (AssertableJson $json) =>
                $json->where('data.id', $order->id)
                     ->where('data.price_type', PriceType::SUBSCRIPTION->value)
                     ->etc()
            );
    }

    public function test_show_order_summary_with_invalid_price_type(): void
    {
        // Arrange
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $user->sites()->attach($site);
        $order = Order::factory()->create([
            'price_type' => 'invalid_type',
            'site_id' => $site->id,
        ]);

        // Act
        $response = $this
            ->getJson(route('api.order-summary.show', $order));

        // Assert
        $response->assertStatus(500);
    }

    public function test_show_order_summary_unauthenticated(): void
    {
        // Arrange
        $order = Order::factory()->create();

        // Act
        $response = $this->getJson(route('api.order-summary.show', $order));

        // Assert
        $response->assertStatus(401);
    }
}