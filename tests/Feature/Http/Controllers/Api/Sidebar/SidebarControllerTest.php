<?php

namespace Tests\Feature;

use App\Enums\SiteStatus;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use Laravel\Sanctum\Sanctum;
use App\Services\Admin\Sidebar\SidebarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SidebarControllerTest extends TestCase
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

    public function testIndex(): void
    {
        Sidebar::factory()->count(3)->create(['site_id' => $this->site->id]);


        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );
        $response = $this->getJson(route('sidebar.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data'); // Assuming SidebarResource wraps the data
    }

    public function testShow(): void
    {
        $sidebar = Sidebar::factory()->create(['site_id' => $this->site->id]);

        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );
        $response = $this->getJson(route('sidebar.show', ['sidebar' => $sidebar->id]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'title',
                'icon',
                'properties',
                'roles',
                'widgets',
                'has_permission',
                // Add other attributes as needed based on your SidebarResource
            ],
        ]);
    }

    public function testStore(): void
    {
        $data = [
            'name' => $this->faker->name,
            'title' => $this->faker->sentence,
            'icon' => $this->faker->word,
            'site_id' => $this->site->id,
        ];

        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );
        $response = $this->postJson(route('sidebar.store'), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Sidebar created',
        ]);

        $this->assertDatabaseHas('sidebars', $data + ['site_id' => $this->site->id]); // verify data stored and site_id is set
    }

    public function testStoreValidationError(): void
    {
        $data = []; // Missing required fields

        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );
        $response = $this->postJson(route('sidebar.store'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'name', // Assuming 'name' is a required field
        ]);
    }

    public function testUpdate(): void
    {
        $sidebar = Sidebar::factory()->create(['site_id' => $this->site->id]);
        $data = [
            'name' => $this->faker->name,
            // Add other fields you want to update
        ];

        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );
        $response = $this->patchJson(route('sidebar.update', ['sidebar' => $sidebar->id]), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Sidebar updated',
        ]);

        $this->assertDatabaseHas('sidebars', $data + ['id' => $sidebar->id, 'site_id' => $this->site->id]); //verify updated data.
    }

     public function testUpdateValidationError(): void
    {
        $sidebar = Sidebar::factory()->create(['site_id' => $this->site->id]);
        $data = [
            'name' => null, // Invalid data
        ];

        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );
        $response = $this->patchJson(route('sidebar.update', ['sidebar' => $sidebar->id]), $data);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['name']);
    }


    public function testDestroy(): void
    {
        $sidebar = Sidebar::factory()->create(['site_id' => $this->site->id]);

        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );
        $response = $this->deleteJson(route('sidebar.destroy', ['sidebar' => $sidebar->id]));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Sidebar deleted',
        ]);

        $this->assertDatabaseMissing('sidebars', ['id' => $sidebar->id]);
    }
}