<?php

namespace Tests\Feature;

use App\Models\Country;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CountryControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    
    public function it_can_list_countries()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        Country::factory(3)->create();

        $response = $this->getJson(route('countries.index'));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    // Add other attributes expected in your CountryResource
                ],
            ],
            'links',
            'meta',
        ]);
    }

    
    public function it_can_show_a_country()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $country = Country::factory()->create();

        $response = $this->getJson(route('countries.show', $country));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                // Add other attributes expected in your CountryResource
            ],
        ]);
    }

    
    public function it_can_store_a_country()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $data = [
            'name' => $this->faker->country,
            // Add other required attributes
        ];

        $response = $this->postJson(route('countries.store'), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Country created',
        ]);

        $this->assertDatabaseHas('countries', ['name' => $data['name']]);
    }

    
    public function it_can_update_a_country()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $country = Country::factory()->create();

        $data = [
            'name' => $this->faker->country,
            // Add other attributes to update
        ];

        $response = $this->putJson(route('countries.update', $country), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Country updated',
        ]);

        $this->assertDatabaseHas('countries', ['id' => $country->id, 'name' => $data['name']]);
    }

    
    public function it_can_destroy_a_country()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $country = Country::factory()->create();

        $response = $this->deleteJson(route('countries.destroy', $country));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Country deleted',
        ]);

        $this->assertDatabaseMissing('countries', ['id' => $country->id]);
    }

        
    public function it_returns_unprocessable_entity_when_store_fails()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Assuming StoreCountryRequest requires 'name'
        $data = [];

        $response = $this->postJson(route('countries.store'), $data);

        $response->assertStatus(422);
    }

    
    public function it_returns_unprocessable_entity_when_update_fails()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $country = Country::factory()->create();
        $data = [];

        $response = $this->putJson(route('countries.update', $country), $data);

        $response->assertStatus(422);
    }

    
    public function it_returns_unprocessable_entity_when_delete_fails()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $country = Country::factory()->create();

        // Mock the CountryService to return false, simulating a failure
        $this->app->bind(\App\Services\Locale\CountryService::class, function ($app) {
            $mock = \Mockery::mock(\App\Services\Locale\CountryService::class);
            $mock->shouldReceive('setUser')->andReturnSelf();
            $mock->shouldReceive('setSite')->andReturnSelf();
            $mock->shouldReceive('deleteCountry')->once()->andReturn(false);
            return $mock;
        });

        $response = $this->deleteJson(route('countries.destroy', $country));

        $response->assertStatus(422);
        $response->assertJson([
            'message' => 'Error deleting country',
        ]);
    }
}