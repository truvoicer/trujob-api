<?php

namespace Tests\Feature\Api\Sidebar;

use App\Enums\SiteStatus;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use App\Services\Admin\Sidebar\SidebarService;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SidebarBulkDeleteControllerTest extends TestCase
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

    public function test_it_can_bulk_delete_sidebars(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        // Create some sidebars to delete
        $sidebars = Sidebar::factory(3)->create(['site_id' => $this->site->id]);
        $sidebarIds = $sidebars->pluck('id')->toArray();

        $response = $this->deleteJson(route('sidebar.bulk.destroy'), ['ids' => $sidebarIds]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(['message' => 'Sidebars deleted successfully.']);

        $this->assertDatabaseMissing('sidebars', ['id' => $sidebarIds[0]]);
        $this->assertDatabaseMissing('sidebars', ['id' => $sidebarIds[1]]);
        $this->assertDatabaseMissing('sidebars', ['id' => $sidebarIds[2]]);
    }


    public function test_it_returns_error_if_bulk_delete_fails(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        // Create some sidebars to delete
        $sidebars = Sidebar::factory(3)->create(['site_id' => $this->site->id]);
        $sidebarIds = $sidebars->pluck('id')->toArray();

        // Mock the repository to simulate a failure
        $this->partialMock(SidebarService::class, function ($mock) {
            $mock->shouldReceive('deleteBulkSidebars')->andReturn(false); // Simulate a failed deletion
        });

        $response = $this->deleteJson(route('sidebar.bulk.destroy'), ['ids' => $sidebarIds]);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['message' => 'Sidebars could not be deleted.']);

        // Check that the sidebars still exist (since deletion failed)
        $this->assertDatabaseHas('sidebars', ['id' => $sidebarIds[0]]);
        $this->assertDatabaseHas('sidebars', ['id' => $sidebarIds[1]]);
        $this->assertDatabaseHas('sidebars', ['id' => $sidebarIds[2]]);
    }


    public function test_it_validates_the_ids_are_required(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        $response = $this->deleteJson(route('sidebar.bulk.destroy'), []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['ids']);
    }


    public function test_it_validates_the_ids_are_an_array(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        $response = $this->deleteJson(route('sidebar.bulk.destroy'), ['ids' => 'not an array']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['ids']);
    }
}
