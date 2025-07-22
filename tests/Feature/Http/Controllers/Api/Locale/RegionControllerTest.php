<?php

namespace Tests\Feature;

use App\Models\Region;

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

class RegionControllerTest extends TestCase
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