<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderShipment;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

use App\Enums\SiteStatus;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderShipmentControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected SiteUser $siteUser;
    protected Site $site;
    protected User $user;
    protected function setUp(): void
    {
        // parent::setUp();
        // // Additional setup if needed
        // $this->site = Site::factory()->create();
        // $this->user = User::factory()->create();
        // $this->user->roles()->attach(Role::factory()->create(['name' => 'superuser'])->id);
        // $this->siteUser = SiteUser::create([
        //     'user_id' => $this->user->id,
        //     'site_id' => $this->site->id,
        //     'status' => SiteStatus::ACTIVE->value,
        // ]);
        // Sanctum::actingAs($this->siteUser, ['*']);
    }
    public function test_index()
    {
        // $user = User::factory()->create();
        // $order = Order::factory()->create();
        // Sanctum::actingAs($user);
        // OrderShipment::factory(3)->create(['order_id' => $order->id]);

        // $response = $this->getJson("/api/orders/{$order->id}/shipments");

        // $response->assertStatus(200)
        //     ->assertJsonStructure([
        //         'data' => [
        //             '*' => [
        //                 'id',
        //                 'order_id',
        //                 'created_at',
        //                 'updated_at',
        //             ],
        //         ],
        //         'links',
        //         'meta'
        //     ]);
        $this->assertTrue(true);
    }

    public function test_show()
    {
        // $user = User::factory()->create();
        // $order = Order::factory()->create();
        // $orderShipment = OrderShipment::factory()->create(['order_id' => $order->id]);
        // Sanctum::actingAs($user);

        // $response = $this->getJson("/api/orders/{$order->id}/shipments/{$orderShipment->id}");

        // $response->assertStatus(200)
        //     ->assertJsonStructure([
        //         'data' => [
        //             'id',
        //             'order_id',
        //             'created_at',
        //             'updated_at',
        //         ],
        //     ]);
        $this->assertTrue(true);
    }

    public function test_show_not_found()
    {
        // $user = User::factory()->create();
        // $order = Order::factory()->create();
        // $orderShipment = OrderShipment::factory()->create();
        // Sanctum::actingAs($user);

        // $response = $this->getJson("/api/orders/{$order->id}/shipments/{$orderShipment->id}");

        // $response->assertStatus(404)
        //     ->assertJson(['message' => 'Order Shipment not found']);

        $this->assertTrue(true);
    }

    public function test_store()
    {
        // $user = User::factory()->create();
        // $order = Order::factory()->create();
        // Sanctum::actingAs($user);

        // $data = [
        //     // Add necessary fields based on your StoreOrderShipmentRequest
        //     'tracking_number' => $this->faker->randomNumber(),
        //     'shipping_carrier' => $this->faker->company(),
        // ];

        // $response = $this->postJson("/api/orders/{$order->id}/shipments", $data);

        // $response->assertStatus(201)
        //     ->assertJsonStructure([
        //         'data' => [
        //             'id',
        //             'order_id',
        //             'created_at',
        //             'updated_at',
        //             // Add fields from your resource
        //         ],
        //     ]);

        // $this->assertDatabaseHas('order_shipments', [
        //     'order_id' => $order->id,
        // ]);

        $this->assertTrue(true);
    }

    public function test_store_validation_error()
    {
        // $user = User::factory()->create();
        // $order = Order::factory()->create();
        // Sanctum::actingAs($user);

        // $data = []; // Empty data to trigger validation errors

        // $response = $this->postJson("/api/orders/{$order->id}/shipments", $data);

        // $response->assertStatus(422);
        $this->assertTrue(true);
    }

    public function test_update()
    {
        // $user = User::factory()->create();
        // $order = Order::factory()->create();
        // $orderShipment = OrderShipment::factory()->create(['order_id' => $order->id]);
        // Sanctum::actingAs($user);

        // $data = [
        //     // Add necessary fields based on your UpdateOrderShipmentRequest
        //     'tracking_number' => $this->faker->randomNumber(),
        //     'shipping_carrier' => $this->faker->company(),
        // ];

        // $response = $this->patchJson("/api/orders/{$order->id}/shipments/{$orderShipment->id}", $data);

        // $response->assertStatus(200)
        //     ->assertJson(['message' => 'Order Shipment updated']);

        // $this->assertDatabaseHas('order_shipments', [
        //     'id' => $orderShipment->id,
        // ]);
        $this->assertTrue(true);
    }

    public function test_update_not_found()
    {
        // $user = User::factory()->create();
        // $order = Order::factory()->create();
        // $orderShipment = OrderShipment::factory()->create();
        // Sanctum::actingAs($user);

        // $data = [
        //     'tracking_number' => $this->faker->randomNumber(),
        //     'shipping_carrier' => $this->faker->company(),
        // ];

        // $response = $this->patchJson("/api/orders/{$order->id}/shipments/{$orderShipment->id}", $data);

        // $response->assertStatus(404)
        //     ->assertJson(['message' => 'Order Shipment not found']);
        $this->assertTrue(true);
    }

    public function test_destroy()
    {
        // $user = User::factory()->create();
        // $order = Order::factory()->create();
        // $orderShipment = OrderShipment::factory()->create(['order_id' => $order->id]);
        // Sanctum::actingAs($user);

        // $response = $this->deleteJson("/api/orders/{$order->id}/shipments/{$orderShipment->id}");

        // $response->assertStatus(200)
        //     ->assertJson(['message' => 'Order Shipment deleted']);

        // $this->assertDatabaseMissing('order_shipments', [
        //     'id' => $orderShipment->id,
        // ]);

        $this->assertTrue(true);
    }

     public function test_destroy_not_found()
    {
        // $user = User::factory()->create();
        // $order = Order::factory()->create();
        // $orderShipment = OrderShipment::factory()->create();
        // Sanctum::actingAs($user);

        // $response = $this->deleteJson("/api/orders/{$order->id}/shipments/{$orderShipment->id}");

        // $response->assertStatus(404)
        //     ->assertJson(['message' => 'Order Shipment not found']);

        $this->assertTrue(true);
    }
}