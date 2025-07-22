<?php

namespace Tests\Feature;

use App\Models\Discount;
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
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OrderDiscountControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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
    public function test_index_returns_discounts_for_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $discounts = Discount::factory(3)->create();
        $order->discounts()->attach($discounts->pluck('id')->toArray());

        $response = $this
            ->getJson(route('orders.discounts.index', $order));

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_store_attaches_discount_to_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $discount = Discount::factory()->create();

        $response = $this
            ->postJson(route('orders.discounts.store', [$order, $discount]));

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Order discount created']);
        $this->assertDatabaseHas('discount_order', [
            'order_id' => $order->id,
            'discount_id' => $discount->id,
        ]);
    }

    public function test_store_returns_error_if_discount_already_exists_in_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $discount = Discount::factory()->create();
        $order->discounts()->attach($discount->id);

        $response = $this
            ->postJson(route('orders.discounts.store', [$order, $discount]));

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Discount already exists in order']);
    }

    public function test_destroy_detaches_discount_from_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $discount = Discount::factory()->create();
        $order->discounts()->attach($discount->id);

        $response = $this
            ->deleteJson(route('orders.discounts.destroy', [$order, $discount]));

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Order discount deleted']);
        $this->assertDatabaseMissing('discount_order', [
            'order_id' => $order->id,
            'discount_id' => $discount->id,
        ]);
    }
}