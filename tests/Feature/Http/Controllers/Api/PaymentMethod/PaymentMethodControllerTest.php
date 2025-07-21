<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PaymentMethodControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    
    public function it_can_list_payment_methods()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        PaymentMethod::factory()->count(3)->create();

        $response = $this->getJson(route('payment-methods.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                ],
            ],
            'links',
            'meta',
        ]);
    }

    
    public function it_can_show_a_payment_method()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $paymentMethod = PaymentMethod::factory()->create();

        $response = $this->getJson(route('payment-methods.show', $paymentMethod));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    
    public function it_can_create_a_payment_method()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $data = [
            'name' => $this->faker->name,
        ];

        $response = $this->postJson(route('payment-methods.store'), $data);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'PaymentMethod created',
        ]);
        $this->assertDatabaseHas('payment_methods', $data);
    }

    
    public function it_can_update_a_payment_method()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $paymentMethod = PaymentMethod::factory()->create();

        $data = [
            'name' => $this->faker->name,
        ];

        $response = $this->putJson(route('payment-methods.update', $paymentMethod), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'PaymentMethod updated',
        ]);
        $this->assertDatabaseHas('payment_methods', $data);
    }

    
    public function it_can_delete_a_payment_method()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $paymentMethod = PaymentMethod::factory()->create();

        $response = $this->deleteJson(route('payment-methods.destroy', $paymentMethod));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'PaymentMethod deleted',
        ]);
        $this->assertDatabaseMissing('payment_methods', ['id' => $paymentMethod->id]);
    }

    
    public function it_validates_store_request()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->postJson(route('payment-methods.store'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }

    
    public function it_validates_update_request()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');
        $paymentMethod = PaymentMethod::factory()->create();

        $response = $this->putJson(route('payment-methods.update', $paymentMethod), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }
}