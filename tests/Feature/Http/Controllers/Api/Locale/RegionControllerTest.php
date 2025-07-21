<?php

namespace Tests\Feature;

use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_can_list_regions()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        Region::factory(3)->create();

        $response = $this->getJson(route('regions.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
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

    /** @test */
    public function it_can_show_a_region()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $region = Region::factory()->create();

        $response = $this->getJson(route('regions.show', $region));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }

    /** @test */
    public function it_can_store_a_region()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $data = [
            'name' => $this->faker->country,
        ];

        $response = $this->postJson(route('regions.store'), $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Region created',
            ]);

        $this->assertDatabaseHas('regions', $data);
    }

     /** @test */
    public function it_returns_an_error_if_store_fails()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $data = [
            'name' => null, // will cause validation to fail
        ];

        $response = $this->postJson(route('regions.store'), $data);

        $response->assertStatus(422);
    }

    /** @test */
    public function it_can_update_a_region()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $region = Region::factory()->create();

        $data = [
            'name' => $this->faker->country,
        ];

        $response = $this->putJson(route('regions.update', $region), $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Region updated',
            ]);

        $this->assertDatabaseHas('regions', $data);
    }

    /** @test */
    public function it_returns_an_error_if_update_fails()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $region = Region::factory()->create();

        $data = [
            'name' => null,
        ];

        $response = $this->putJson(route('regions.update', $region), $data);

        $response->assertStatus(422);

    }

    /** @test */
    public function it_can_delete_a_region()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $region = Region::factory()->create();

        $response = $this->deleteJson(route('regions.destroy', $region));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Region deleted',
            ]);

        $this->assertDatabaseMissing('regions', ['id' => $region->id]);
    }

    /** @test */
    public function it_returns_an_error_if_delete_fails()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $region = Region::factory()->create();

        // Mocking the service to always return false (delete fails)
        $this->app->bind(\App\Services\Region\RegionService::class, function ($app) use ($region) {
            $mock = \Mockery::mock(\App\Services\Region\RegionService::class);
            $mock->shouldReceive('setUser')->andReturnSelf();
            $mock->shouldReceive('setSite')->andReturnSelf();
            $mock->shouldReceive('deleteRegion')->with($region)->andReturn(false);
            return $mock;
        });

        $response = $this->deleteJson(route('regions.destroy', $region));

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'Error deleting region',
            ]);
    }
}