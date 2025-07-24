<?php

namespace Tests\Feature\Api\Order\Discount;

use App\Models\Order;

use App\Enums\SiteStatus;
use App\Models\Currency;
use App\Models\Discount;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkOrderDiscountControllerTest extends TestCase
{
    use RefreshDatabase;

    protected SiteUser $siteUser;
    protected Site $site;
    protected User $user;
    protected Currency $currency;

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
            'name' => 'US Dollar',
            'code' => 'USD',
            'symbol' => '$',
        ]);
    }
    
    public function test_it_can_sync_discounts_to_an_order()
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $this->currency->id,
        ]);
        $discounts = Discount::factory()->count(3)->create([
            'currency_id' => $this->currency->id,
        ]);
        $discountIds = $discounts->pluck('id')->toArray();

        $response = $this
            ->postJson(route('order.discount.bulk.store', $order), [
                'ids' => $discountIds,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Order discount synced successfully',
            ]);
    }

     
    //  public function test_it_returns_error_when_syncing_discounts_fails()
    //  {

    //     Sanctum::actingAs($this->siteUser, ['*']);
    //      $order = Order::factory()->create([
    //         'user_id' => $this->user->id,
    //         'currency_id' => $this->currency->id,
    //     ]);
    //     $discounts = Discount::factory()->count(3)->create([
    //         'currency_id' => $this->currency->id,
    //     ]);
    //     $discountIds = $discounts->pluck('id')->toArray();

    //      // Mock the OrderService to simulate a failure
    //      $this->mock(\App\Services\Order\OrderService::class, function ($mock) {
    //          $mock->shouldReceive('setUser')
    //              ->andReturnSelf();
    //          $mock->shouldReceive('setSite')
    //              ->andReturnSelf();
    //          $mock->shouldReceive('syncDiscounts')
    //              ->andReturn(false); // Simulate failure
    //      });

    //      $response = $this
    //          ->postJson(route('order.discount.bulk.store', $order), [
    //              'ids' => $discountIds,
    //          ]);

    //      $response->assertStatus(500)
    //          ->assertJson([
    //              'message' => 'Error syncing discount to order',
    //          ]);
    //  }


    
    public function test_it_requires_authentication()
    {

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $this->currency->id,
        ]);

        $response = $this->postJson(route('order.discount.bulk.store', $order), [
            'ids' => [1, 2, 3],
        ]);

        $response->assertStatus(401); // Or 403 depending on your auth setup
    }

    
    public function test_it_validates_the_request()
    {

        Sanctum::actingAs($this->siteUser, ['*']);
        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'currency_id' => $this->currency->id,
        ]);

        $response = $this
            ->postJson(route('order.discount.bulk.store', $order), [
                'ids' => 'not an array',
            ]);

        $response->assertStatus(422); // Unprocessable Entity
        $response->assertJsonValidationErrors(['ids']);
    }

    // Optionally, add tests for authorization if applicable
}