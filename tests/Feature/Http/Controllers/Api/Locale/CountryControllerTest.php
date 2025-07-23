<?php

namespace Tests\Feature\Http\Controllers\Api\Locale;

use App\Models\Country;

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

class CountryControllerTest extends TestCase
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
    
    public function test_it_can_list_countries()
    {
        $user = User::factory()->create();
       
        Sanctum::actingAs($this->siteUser, ['*']);

        Country::factory(3)->create();

        $response = $this->getJson(route('locale.country.index'));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    "name",
                    "iso2",
                    "iso3",
                    "phone_code",
                    "is_active",
                    "created_at",
                    "updated_at"
                ],
            ],
            'links',
            'meta',
        ]);
    }

    
    public function test_it_can_show_a_country()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($this->siteUser, ['*']);

        $country = Country::factory()->create();

        $response = $this->getJson(route('locale.country.show', $country));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                // Add other attributes expected in your CountryResource
            ],
        ]);
    }

    
    public function test_it_can_store_a_country()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($this->siteUser, ['*']);

        $data = [
            'name' => $this->faker->country,
            'iso2' => $this->faker->countryCode,
            'iso3' => $this->faker->countryISOAlpha3,
            'phone_code' => $this->faker->text(5),
            'is_active' => $this->faker->boolean,
        ];

        $response = $this->postJson(route('locale.country.store'), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Country created',
        ]);

        $this->assertDatabaseHas('countries', ['name' => $data['name']]);
    }

    
    public function test_it_can_update_a_country()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($this->siteUser, ['*']);

        $country = Country::factory()->create();

        $data = [
            'name' => $this->faker->country,
            // Add other attributes to update
        ];

        $response = $this->patchJson(route('locale.country.update', $country), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Country updated',
        ]);

        $this->assertDatabaseHas('countries', ['id' => $country->id, 'name' => $data['name']]);
    }

    
    public function test_it_can_destroy_a_country()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($this->siteUser, ['*']);

        $country = Country::factory()->create();

        $response = $this->deleteJson(route('locale.country.destroy', $country));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Country deleted',
        ]);

        $this->assertDatabaseMissing('countries', ['id' => $country->id]);
    }

        
    public function test_it_returns_unprocessable_entity_when_store_fails()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($this->siteUser, ['*']);

        // Assuming StoreCountryRequest requires 'name'
        $data = [];

        $response = $this->postJson(route('locale.country.store'), $data);

        $response->assertStatus(422);
    }

    
    public function test_it_returns_unprocessable_entity_when_update_fails()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($this->siteUser, ['*']);

        $country = Country::factory()->create();
        $data = [];

        $response = $this->patchJson(route('locale.country.update', $country), $data);

        $response->assertStatus(422);
    }

    
    public function test_it_returns_unprocessable_entity_when_delete_fails()
    {
        $user = User::factory()->create();

        Sanctum::actingAs($this->siteUser, ['*']);

        $country = Country::factory()->create();

        // Mock the CountryService to return false, simulating a failure
        $this->app->bind(\App\Services\Locale\CountryService::class, function ($app) {
            $mock = \Mockery::mock(\App\Services\Locale\CountryService::class);
            $mock->shouldReceive('setUser')->andReturnSelf();
            $mock->shouldReceive('setSite')->andReturnSelf();
            $mock->shouldReceive('deleteCountry')->once()->andReturn(false);
            return $mock;
        });

        $response = $this->deleteJson(route('locale.country.destroy', $country));

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Error deleting country',
        ]);
    }
}