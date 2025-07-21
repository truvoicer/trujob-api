<?php

namespace Tests\Feature\Api\Order\Discount;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BulkOrderDiscountControllerTest extends TestCase
{
    use RefreshDatabase;

    
    public function it_can_sync_discounts_to_an_order()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();
        $discountIds = [1, 2, 3];

        $response = $this->actingAs($user)
            ->postJson(route('orders.bulk-discounts', $order), [
                'ids' => $discountIds,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Order discount synced successfully',
            ]);
    }

     
     public function it_returns_error_when_syncing_discounts_fails()
     {
         $user = User::factory()->create();
         $order = Order::factory()->create();
         $discountIds = [1, 2, 3];

         // Mock the OrderService to simulate a failure
         $this->mock(\App\Services\Order\OrderService::class, function ($mock) {
             $mock->shouldReceive('setUser')
                 ->andReturnSelf();
             $mock->shouldReceive('setSite')
                 ->andReturnSelf();
             $mock->shouldReceive('syncDiscounts')
                 ->andReturn(false); // Simulate failure
         });

         $response = $this->actingAs($user)
             ->postJson(route('orders.bulk-discounts', $order), [
                 'ids' => $discountIds,
             ]);

         $response->assertStatus(500)
             ->assertJson([
                 'message' => 'Error syncing discount to order',
             ]);
     }


    
    public function it_requires_authentication()
    {
        $order = Order::factory()->create();

        $response = $this->postJson(route('orders.bulk-discounts', $order), [
            'ids' => [1, 2, 3],
        ]);

        $response->assertStatus(401); // Or 403 depending on your auth setup
    }

    
    public function it_validates_the_request()
    {
        $user = User::factory()->create();
        $order = Order::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('orders.bulk-discounts', $order), [
                'ids' => 'not an array',
            ]);

        $response->assertStatus(422); // Unprocessable Entity
        $response->assertJsonValidationErrors(['ids']);
    }

    // Optionally, add tests for authorization if applicable
}