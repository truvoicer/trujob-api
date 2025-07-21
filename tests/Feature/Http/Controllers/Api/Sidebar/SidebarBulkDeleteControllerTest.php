<?php

namespace Tests\Feature\Api\Sidebar;

use App\Models\Sidebar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class SidebarBulkDeleteControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_can_bulk_delete_sidebars(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create some sidebars to delete
        $sidebars = Sidebar::factory(3)->create(['site_id' => $user->site_id]);
        $sidebarIds = $sidebars->pluck('id')->toArray();

        $response = $this->postJson(route('api.sidebar.bulk-delete'), ['ids' => $sidebarIds]);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJson(['message' => 'Sidebars deleted successfully.']);

        $this->assertDatabaseMissing('sidebars', ['id' => $sidebarIds[0]]);
        $this->assertDatabaseMissing('sidebars', ['id' => $sidebarIds[1]]);
        $this->assertDatabaseMissing('sidebars', ['id' => $sidebarIds[2]]);
    }

    /** @test */
    public function it_returns_error_if_bulk_delete_fails(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // Create some sidebars to delete
        $sidebars = Sidebar::factory(3)->create(['site_id' => $user->site_id]);
        $sidebarIds = $sidebars->pluck('id')->toArray();

        // Mock the repository to simulate a failure
        $this->partialMock(\App\Repositories\SidebarRepository::class, function ($mock) {
            $mock->shouldReceive('delete')->andReturn(false); // Simulate a failed deletion
        });

        $response = $this->postJson(route('api.sidebar.bulk-delete'), ['ids' => $sidebarIds]);

        $response->assertStatus(Response::HTTP_INTERNAL_SERVER_ERROR)
            ->assertJson(['message' => 'Sidebars could not be deleted.']);

        // Check that the sidebars still exist (since deletion failed)
        $this->assertDatabaseHas('sidebars', ['id' => $sidebarIds[0]]);
        $this->assertDatabaseHas('sidebars', ['id' => $sidebarIds[1]]);
        $this->assertDatabaseHas('sidebars', ['id' => $sidebarIds[2]]);
    }

    /** @test */
    public function it_validates_the_ids_are_required(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('api.sidebar.bulk-delete'), []);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['ids']);
    }

    /** @test */
    public function it_validates_the_ids_are_an_array(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->postJson(route('api.sidebar.bulk-delete'), ['ids' => 'not an array']);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonValidationErrors(['ids']);
    }
}