<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Sidebar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarRoleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndexReturnsRolesForSidebar(): void
    {
        $user = User::factory()->create();
        $sidebar = Sidebar::factory()->create();
        $role1 = Role::factory()->create();
        $role2 = Role::factory()->create();

        $sidebar->roles()->attach([$role1->id, $role2->id]);

        $response = $this->actingAs($user)
            ->getJson(route('sidebars.roles.index', $sidebar));

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
            ]);

        $this->assertCount(2, $response->json('data'));
    }

    public function testStoreAssignsRoleToSidebar(): void
    {
        $user = User::factory()->create();
        $sidebar = Sidebar::factory()->create();
        $role = Role::factory()->create();

        $response = $this->actingAs($user)
            ->postJson(route('sidebars.roles.store', ['sidebar' => $sidebar, 'role' => $role]));

        $response->assertStatus(200)
            ->assertJson([
                'message' => "Role assigned to sidebar.",
            ]);

        $this->assertDatabaseHas('role_sidebar', [
            'sidebar_id' => $sidebar->id,
            'role_id' => $role->id,
        ]);
    }

    public function testDestroyRemovesRoleFromSidebar(): void
    {
        $user = User::factory()->create();
        $sidebar = Sidebar::factory()->create();
        $role = Role::factory()->create();

        $sidebar->roles()->attach($role->id);

        $this->assertDatabaseHas('role_sidebar', [
            'sidebar_id' => $sidebar->id,
            'role_id' => $role->id,
        ]);

        $response = $this->actingAs($user)
            ->deleteJson(route('sidebars.roles.destroy', ['sidebar' => $sidebar, 'role' => $role]));

        $response->assertStatus(200)
            ->assertJson([
                'message' => "Role removed from sidebar.",
            ]);

        $this->assertDatabaseMissing('role_sidebar', [
            'sidebar_id' => $sidebar->id,
            'role_id' => $role->id,
        ]);
    }
}