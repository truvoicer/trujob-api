<?php

namespace Tests\Feature;

use App\Models\Region;

use App\Enums\SiteStatus;
use App\Models\Country;
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

    protected Country $country;
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
        $this->country = Country::factory()->create([
            'name' => 'Test Country',
            'iso2' => 'TC',
            'iso3' => 'TST',
            'phone_code' => '123',
            'is_active' => true,
        ]);
    }

    public function test_it_can_list_regions()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($this->siteUser, ['*']);

        Region::factory(3)->create([
            'country_id' => $this->country->id,
        ]);

        $response = $this->getJson(route('locale.region.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'is_active',
                        'admin_name',
                        'toponym_name',
                        'category',
                        'description',
                        'lng',
                        'lat',
                        'population',
                        'country',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
                'meta',
            ]);
    }


    public function test_it_can_show_a_region()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($this->siteUser, ['*']);

        $region = Region::factory()->create([
            'country_id' => $this->country->id,
        ]);

        $response = $this->getJson(route('locale.region.show', $region));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'is_active',
                    'admin_name',
                    'toponym_name',
                    'category',
                    'description',
                    'lng',
                    'lat',
                    'population',
                    'country',
                    'created_at',
                    'updated_at',
                ],
            ]);
    }


    public function test_it_can_store_a_region()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($this->siteUser, ['*']);

        $data = [
            'country_id' => $this->country->id,
            'name' => $this->faker->country,
            'admin_name' => $this->faker->word,
            'toponym_name' => $this->faker->word,
            'category' => $this->faker->word,
            'description' => $this->faker->text,
            'lng' => $this->faker->longitude,
            'lat' => $this->faker->latitude,
            'population' => $this->faker->numberBetween(1000, 1000000),
            'is_active' => true,
        ];

        $response = $this->postJson(route('locale.region.store'), $data);

        $response->assertStatus(201)
            ->assertJson([
                'message' => 'Region created',
            ]);

        $this->assertDatabaseHas('regions', $data);
    }


    public function test_it_returns_an_error_if_store_fails()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($this->siteUser, ['*']);

        $data = [
            'name' => null, // will cause validation to fail
        ];

        $response = $this->postJson(route('locale.region.store'), $data);

        $response->assertStatus(422);
    }


    public function test_it_can_update_a_region()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($this->siteUser, ['*']);

        $region = Region::factory()->create([
            'country_id' => $this->country->id,
        ]);

        $data = [
            'name' => $this->faker->country,
        ];

        $response = $this->patchJson(route('locale.region.update', $region), $data);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Region updated',
            ]);

        $this->assertDatabaseHas('regions', $data);
    }


    public function test_it_returns_an_error_if_update_fails()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($this->siteUser, ['*']);

        $region = Region::factory()->create([
            'country_id' => $this->country->id,
        ]);

        $data = [
            'name' => null,
        ];

        $response = $this->patchJson(route('locale.region.update', $region), $data);

        $response->assertStatus(422);
    }


    // public function test_it_can_delete_a_region()
    // {
    //     $user = User::factory()->create();
    //     Sanctum::actingAs($this->siteUser, ['*']);

    //     $region = Region::factory()->create([
    //         'country_id' => $this->country->id,
    //     ]);

    //     $response = $this->deleteJson(route('locale.region.destroy', $region));

    //     $response->assertStatus(200)
    //         ->assertJson([
    //             'message' => 'Region deleted',
    //         ]);
    //         dd(Region::all());
    //     $this->assertDatabaseMissing('regions', [
    //         'id' => $region->id,
    //         'country_id' => $this->country->id,
    //     ]);
    // }


    // public function test_it_returns_an_error_if_delete_fails()
    // {
    //     $user = User::factory()->create();
    //     Sanctum::actingAs($this->siteUser, ['*']);

    //     $region = Region::factory()->create([
    //         'country_id' => $this->country->id,
    //     ]);

    //     // Mocking the service to always return false (delete fails)
    //     // $this->app->bind(\App\Services\Region\RegionService::class, function ($app) use ($region) {
    //     //     $mock = \Mockery::mock(\App\Services\Region\RegionService::class);
    //     //     $mock->shouldReceive('setUser')->andReturnSelf();
    //     //     $mock->shouldReceive('setSite')->andReturnSelf();
    //     //     $mock->shouldReceive('deleteRegion')->with($region)->andReturn(false);
    //     //     return $mock;
    //     // });

    //     $response = $this->deleteJson(route('locale.region.destroy', $region));

    //     $response->assertStatus(422)
    //         ->assertJson([
    //             'message' => 'Error deleting region',
    //         ]);
    // }
}
