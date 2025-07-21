<?php

namespace Tests\Feature\Api\Order;

use App\Enums\Price\PriceType;
use App\Models\Order;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class OrderSummaryControllerTest extends TestCase
{
    use RefreshDatabase;

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
        $response = $this->actingAs($user)
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
        $response = $this->actingAs($user)
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
        $response = $this->actingAs($user)
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