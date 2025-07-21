<?php

namespace Tests\Feature;

use App\Models\Sidebar;
use App\Models\Site;
use App\Models\User;
use App\Services\Admin\Sidebar\SidebarService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class SidebarControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;
    protected Site $site;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->site = Site::factory()->create();
        $this->user->site_id = $this->site->id;
        $this->user->save();

        $this->actingAs($this->user); // Authenticate the user for the tests
    }

    public function testIndex(): void
    {
        Sidebar::factory()->count(3)->create(['site_id' => $this->site->id]);
        Sidebar::factory()->count(2)->create(); // Other sidebars

        $response = $this->getJson(route('sidebar.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data'); // Assuming SidebarResource wraps the data
    }

    public function testShow(): void
    {
        $sidebar = Sidebar::factory()->create(['site_id' => $this->site->id]);

        $response = $this->getJson(route('sidebar.show', ['sidebar' => $sidebar->id]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'site_id',
                'created_at',
                'updated_at',
                // Add other attributes as needed based on your SidebarResource
            ],
        ]);
    }

    public function testStore(): void
    {
        $data = [
            'name' => $this->faker->name,
            // Add other required fields from your CreateSidebarRequest
        ];

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

        $response = $this->putJson(route('sidebar.update', ['sidebar' => $sidebar->id]), $data);

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

        $response = $this->putJson(route('sidebar.update', ['sidebar' => $sidebar->id]), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }


    public function testDestroy(): void
    {
        $sidebar = Sidebar::factory()->create(['site_id' => $this->site->id]);

        $response = $this->deleteJson(route('sidebar.destroy', ['sidebar' => $sidebar->id]));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Sidebar deleted',
        ]);

        $this->assertDatabaseMissing('sidebars', ['id' => $sidebar->id]);
    }
}