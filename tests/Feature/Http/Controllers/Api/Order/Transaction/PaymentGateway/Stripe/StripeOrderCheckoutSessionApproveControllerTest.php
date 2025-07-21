<?php

namespace Tests\Feature\Api\Order\Transaction\PaymentGateway\Stripe;

use App\Enums\Price\PriceType;
use App\Models\Order;
use App\Models\Site;
use App\Models\Transaction;
use App\Models\User;
use App\Services\Payment\Stripe\StripeOrderService;
use App\Services\Payment\Stripe\StripeSubscriptionOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class StripeOrderCheckoutSessionApproveControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_store_one_time_success(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Site $site */
        $site = Site::factory()->create();
        $user->sites()->attach($site);

        /** @var Order $order */
        $order = Order::factory()->create([
            'price_type' => PriceType::ONE_TIME,
        ]);
        /** @var Transaction $transaction */
        $transaction = Transaction::factory()->create([
            'order_id' => $order->id,
        ]);

        $mockStripeOrderService = Mockery::mock(StripeOrderService::class, function (MockInterface $mock) {
            $mock->shouldReceive('setUser')->once();
            $mock->shouldReceive('setSite')->once();
            $mock->shouldReceive('createCheckoutSession')
                ->once()
                ->andReturn((object)['id' => 'cs_test', 'client_secret' => 'secret_test']);
        });

        $this->app->instance(StripeOrderService::class, $mockStripeOrderService);

        $response = $this->actingAs($user)
            ->postJson(route('api.order.transaction.payment-gateway.stripe.store', ['order' => $order->id, 'transaction' => $transaction->id]), []);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Stripe checkout session created',
                'data' => [
                    'id' => 'cs_test',
                    'client_secret' => 'secret_test',
                ],
            ]);
    }

    public function test_store_one_time_failure(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Site $site */
        $site = Site::factory()->create();
        $user->sites()->attach($site);

        /** @var Order $order */
        $order = Order::factory()->create([
            'price_type' => PriceType::ONE_TIME,
        ]);
        /** @var Transaction $transaction */
        $transaction = Transaction::factory()->create([
            'order_id' => $order->id,
        ]);

        $mockStripeOrderService = Mockery::mock(StripeOrderService::class, function (MockInterface $mock) {
            $mock->shouldReceive('setUser')->once();
            $mock->shouldReceive('setSite')->once();
            $mock->shouldReceive('createCheckoutSession')
                ->once()
                ->andReturn(false);
        });

        $this->app->instance(StripeOrderService::class, $mockStripeOrderService);

        $response = $this->actingAs($user)
            ->postJson(route('api.order.transaction.payment-gateway.stripe.store', ['order' => $order->id, 'transaction' => $transaction->id]), []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'success' => true,
                'message' => 'Error creating Stripe checkout session',
                'data' => null,
            ]);
    }

    public function test_store_subscription_success(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Site $site */
        $site = Site::factory()->create();
        $user->sites()->attach($site);

        /** @var Order $order */
        $order = Order::factory()->create([
            'price_type' => PriceType::SUBSCRIPTION,
        ]);
        /** @var Transaction $transaction */
        $transaction = Transaction::factory()->create([
            'order_id' => $order->id,
        ]);

        $mockStripeSubscriptionOrderService = Mockery::mock(StripeSubscriptionOrderService::class, function (MockInterface $mock) {
            $mock->shouldReceive('setUser')->once();
            $mock->shouldReceive('setSite')->once();
            $mock->shouldReceive('handleSubscriptionApproval')
                ->once()
                ->andReturn(['subscription_id' => 'sub_test']);
        });

        $this->app->instance(StripeSubscriptionOrderService::class, $mockStripeSubscriptionOrderService);

        $response = $this->actingAs($user)
            ->postJson(route('api.order.transaction.payment-gateway.stripe.store', ['order' => $order->id, 'transaction' => $transaction->id]), []);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJson([
                'success' => true,
                'message' => 'Stripe subscription checkout session approved',
                'data' => ['subscription_id' => 'sub_test'],
            ]);
    }

    public function test_store_subscription_failure(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Site $site */
        $site = Site::factory()->create();
        $user->sites()->attach($site);

        /** @var Order $order */
        $order = Order::factory()->create([
            'price_type' => PriceType::SUBSCRIPTION,
        ]);
        /** @var Transaction $transaction */
        $transaction = Transaction::factory()->create([
            'order_id' => $order->id,
        ]);

        $mockStripeSubscriptionOrderService = Mockery::mock(StripeSubscriptionOrderService::class, function (MockInterface $mock) {
            $mock->shouldReceive('setUser')->once();
            $mock->shouldReceive('setSite')->once();
            $mock->shouldReceive('handleSubscriptionApproval')
                ->once()
                ->andReturn(false);
        });

        $this->app->instance(StripeSubscriptionOrderService::class, $mockStripeSubscriptionOrderService);

        $response = $this->actingAs($user)
            ->postJson(route('api.order.transaction.payment-gateway.stripe.store', ['order' => $order->id, 'transaction' => $transaction->id]), []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'success' => true,
                'message' => 'Error creating Stripe subscription checkout session',
                'data' => null,
            ]);
    }

    public function test_store_invalid_price_type(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        /** @var Site $site */
        $site = Site::factory()->create();
        $user->sites()->attach($site);

        /** @var Order $order */
        $order = Order::factory()->create([
            'price_type' => 'invalid',
        ]);
        /** @var Transaction $transaction */
        $transaction = Transaction::factory()->create([
            'order_id' => $order->id,
        ]);

        $response = $this->actingAs($user)
            ->postJson(route('api.order.transaction.payment-gateway.stripe.store', ['order' => $order->id, 'transaction' => $transaction->id]), []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJson([
                'message' => 'Invalid price type',
            ]);
    }
}