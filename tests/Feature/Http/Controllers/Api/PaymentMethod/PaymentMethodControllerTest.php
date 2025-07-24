<?php

namespace Tests\Feature;

use App\Models\PaymentMethod;

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

class PaymentMethodControllerTest extends TestCase
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
    }

    public function test_it_can_list_payment_methods()
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        PaymentMethod::factory()->count(3)->create();

        $response = $this->getJson(route('payment-method.index'));

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


    public function test_it_can_show_a_payment_method()
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $paymentMethod = PaymentMethod::factory()->create();

        $response = $this->getJson(route('payment-method.show', $paymentMethod));

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


    public function test_it_can_create_a_payment_method()
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        $data = [
            'name' => $this->faker->name,
            'description' => $this->faker->text,
            'icon' => $this->faker->imageUrl(),
            'is_default' => $this->faker->boolean,
            'is_active' => $this->faker->boolean,
            'settings' => $this->faker->randomElements(['setting1', 'setting2'], 2),
        ];

        $response = $this->postJson(route('payment-method.store'), $data);

        $response->assertStatus(201);
        $response->assertJson([
            'message' => 'PaymentMethod created',
        ]);
        $data['settings'] = json_encode($data['settings']); // Ensure settings is stored as JSON
        $this->assertDatabaseHas('payment_methods', $data);
    }


    public function test_it_can_update_a_payment_method()
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $paymentMethod = PaymentMethod::factory()->create();

        $data = [
            'name' => $this->faker->name,
        ];

        $response = $this->patchJson(route('payment-method.update', $paymentMethod), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'PaymentMethod updated',
        ]);
        $this->assertDatabaseHas('payment_methods', $data);
    }


    public function test_it_can_delete_a_payment_method()
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $paymentMethod = PaymentMethod::factory()->create();

        $response = $this->deleteJson(route('payment-method.destroy', $paymentMethod));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'PaymentMethod deleted',
        ]);
        $this->assertDatabaseMissing('payment_methods', ['id' => $paymentMethod->id]);
    }


    public function test_it_validates_store_request()
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        $response = $this->postJson(route('payment-method.store'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }


    public function test_it_validates_update_request()
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $paymentMethod = PaymentMethod::factory()->create();

        $response = $this->patchJson(route('payment-method.update', $paymentMethod), [
            'name' => '', // Invalid data
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }
}
