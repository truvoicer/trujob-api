<?php

namespace Tests\Feature\Api\Order\Transaction\PaymentGateway\Stripe;

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

use App\Services\Payment\Stripe\StripeOrderService;
use App\Services\Payment\Stripe\StripeSubscriptionOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Mockery;
use Stripe\Checkout\Session;
use Tests\TestCase;

class StripeOrderCheckoutSessionControllerTest extends TestCase
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
    public function test_store_one_time_success(): void
    {
        // Arrange
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $user->sites()->attach($site);

        $order = Order::factory()->create([
            'price_type' => PriceType::ONE_TIME,
        ]);
        $transaction = Transaction::factory()->create([
            'order_id' => $order->id,
        ]);

        $stripeOrderServiceMock = Mockery::mock(StripeOrderService::class);
        $stripeOrderServiceMock->shouldReceive('setUser')->once();
        $stripeOrderServiceMock->shouldReceive('setSite')->once();

        $checkoutSession = new Session('sess_test');
        $checkoutSession->id = 'cs_test';
        $checkoutSession->client_secret = 'cs_test_secret';

        $stripeOrderServiceMock->shouldReceive('createCheckoutSession')
            ->with($order, $transaction)
            ->andReturn($checkoutSession);

        $this->app->instance(StripeOrderService::class, $stripeOrderServiceMock);


        // Act
        $response = $this
            ->postJson(route('api.orders.transactions.stripe.store', ['order' => $order->id, 'transaction' => $transaction->id]));


        // Assert
        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Stripe checkout session created',
                'data' => [
                    'id' => 'cs_test',
                    'client_secret' => 'cs_test_secret',
                ],
            ]);
    }

    public function test_store_subscription_success(): void
    {
        // Arrange
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $user->sites()->attach($site);

        $order = Order::factory()->create([
            'price_type' => PriceType::SUBSCRIPTION,
        ]);
        $transaction = Transaction::factory()->create([
            'order_id' => $order->id,
        ]);

        $stripeSubscriptionOrderServiceMock = Mockery::mock(StripeSubscriptionOrderService::class);
        $stripeSubscriptionOrderServiceMock->shouldReceive('setUser')->once();
        $stripeSubscriptionOrderServiceMock->shouldReceive('setSite')->once();

        $checkoutSession = new Session('sess_test');
        $checkoutSession->id = 'cs_test';
        $checkoutSession->client_secret = 'cs_test_secret';

        $stripeSubscriptionOrderServiceMock->shouldReceive('createSubscription')
            ->with($order, $transaction)
            ->andReturn($checkoutSession);

        $this->app->instance(StripeSubscriptionOrderService::class, $stripeSubscriptionOrderServiceMock);

        // Act
        $response = $this
            ->postJson(route('api.orders.transactions.stripe.store', ['order' => $order->id, 'transaction' => $transaction->id]));

        // Assert
        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'message' => 'Stripe checkout session created',
                'data' => [
                    'id' => 'cs_test',
                    'client_secret' => 'cs_test_secret',
                ],
            ]);
    }

    public function test_store_invalid_price_type(): void
    {
        // Arrange
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $user->sites()->attach($site);

        $order = Order::factory()->create([
            'price_type' => 'INVALID_PRICE_TYPE',
        ]);
        $transaction = Transaction::factory()->create([
            'order_id' => $order->id,
        ]);


        // Act
        $response = $this
            ->postJson(route('api.orders.transactions.stripe.store', ['order' => $order->id, 'transaction' => $transaction->id]));

        // Assert
        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Invalid price type',
            ]);
    }

    public function test_store_one_time_error(): void
    {
        // Arrange
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $user->sites()->attach($site);

        $order = Order::factory()->create([
            'price_type' => PriceType::ONE_TIME,
        ]);
        $transaction = Transaction::factory()->create([
            'order_id' => $order->id,
        ]);

        $stripeOrderServiceMock = Mockery::mock(StripeOrderService::class);
        $stripeOrderServiceMock->shouldReceive('setUser')->once();
        $stripeOrderServiceMock->shouldReceive('setSite')->once();
        $stripeOrderServiceMock->shouldReceive('createCheckoutSession')
            ->with($order, $transaction)
            ->andReturn(false);

        $this->app->instance(StripeOrderService::class, $stripeOrderServiceMock);

        // Act
        $response = $this
            ->postJson(route('api.orders.transactions.stripe.store', ['order' => $order->id, 'transaction' => $transaction->id]));

        // Assert
        $response->assertStatus(422)
            ->assertJson([
                'success' => true,
                'message' => 'Error creating Stripe checkout session',
                'data' => null
            ]);
    }

    public function test_store_subscription_error(): void
    {
        // Arrange
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $user->sites()->attach($site);

        $order = Order::factory()->create([
            'price_type' => PriceType::SUBSCRIPTION,
        ]);
        $transaction = Transaction::factory()->create([
            'order_id' => $order->id,
        ]);

        $stripeSubscriptionOrderServiceMock = Mockery::mock(StripeSubscriptionOrderService::class);
        $stripeSubscriptionOrderServiceMock->shouldReceive('setUser')->once();
        $stripeSubscriptionOrderServiceMock->shouldReceive('setSite')->once();
        $stripeSubscriptionOrderServiceMock->shouldReceive('createSubscription')
            ->with($order, $transaction)
            ->andReturn(false);

        $this->app->instance(StripeSubscriptionOrderService::class, $stripeSubscriptionOrderServiceMock);

        // Act
        $response = $this
            ->postJson(route('api.orders.transactions.stripe.store', ['order' => $order->id, 'transaction' => $transaction->id]));

        // Assert
        $response->assertStatus(422)
            ->assertJson([
                'success' => true,
                'message' => 'Error creating Stripe checkout session',
                'data' => null
            ]);
    }
}