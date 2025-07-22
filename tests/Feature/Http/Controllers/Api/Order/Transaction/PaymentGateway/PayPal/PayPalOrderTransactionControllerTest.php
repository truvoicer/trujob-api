<?php

namespace Tests\Feature;

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
use App\Models\Transaction;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PayPalOrderTransactionControllerTest extends TestCase
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
    
    public function it_can_show_a_paypal_order()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create(['site_id' => $site->id]);
        $transaction = Transaction::factory()->create(['order_id' => $order->id]);

        $response = $this
            ->getJson(route('api.orders.transactions.payment-gateway.paypal.show', ['order' => $order->id, 'transaction' => $transaction->id]));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Order retrieved successfully',
            ]);
    }

    
    public function it_can_store_a_paypal_one_time_order()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create(['site_id' => $site->id, 'price_type' => PriceType::ONE_TIME]);
        $transaction = Transaction::factory()->create(['order_id' => $order->id]);

        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $response = $this
            ->postJson(route('api.orders.transactions.payment-gateway.paypal.store', ['order' => $order->id, 'transaction' => $transaction->id]), $data);

        $response->assertStatus(201);
    }

    
    public function it_can_store_a_paypal_subscription_order()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create(['site_id' => $site->id, 'price_type' => PriceType::SUBSCRIPTION]);
        $transaction = Transaction::factory()->create(['order_id' => $order->id]);

        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $response = $this
            ->postJson(route('api.orders.transactions.payment-gateway.paypal.store', ['order' => $order->id, 'transaction' => $transaction->id]), $data);

        $response->assertStatus(201);
    }

    
    public function it_returns_unprocessable_entity_for_invalid_price_type()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create(['site_id' => $site->id, 'price_type' => 'invalid_type']);
        $transaction = Transaction::factory()->create(['order_id' => $order->id]);

        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $response = $this
            ->postJson(route('api.orders.transactions.payment-gateway.paypal.store', ['order' => $order->id, 'transaction' => $transaction->id]), $data);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid price type',
            ]);
    }

    
    public function it_can_update_a_paypal_order()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create(['site_id' => $site->id]);
        $transaction = Transaction::factory()->create(['order_id' => $order->id]);

        $data = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        $response = $this
            ->putJson(route('api.orders.transactions.payment-gateway.paypal.update', ['order' => $order->id, 'transaction' => $transaction->id]), $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'PaymentGateway updated',
            ]);
    }

    
    public function it_can_destroy_a_paypal_order()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);
        $order = Order::factory()->create(['site_id' => $site->id]);
        $transaction = Transaction::factory()->create(['order_id' => $order->id]);

        $response = $this
            ->deleteJson(route('api.orders.transactions.payment-gateway.paypal.destroy', ['order' => $order->id, 'transaction' => $transaction->id]));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'PaymentGateway deleted',
            ]);
    }
}