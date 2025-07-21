<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\Sidebar;
use App\Models\SidebarWidget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SidebarWidgetRoleControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index_returns_roles_for_sidebar_widget()
    {
        $user = User::factory()->create();
        $sidebar = Sidebar::factory()->create();
        $sidebarWidget = SidebarWidget::factory()->create(['sidebar_id' => $sidebar->id]);
        $roles = Role::factory()->count(3)->create();

        $sidebarWidget->roles()->attach($roles->pluck('id')->toArray());

        $response = $this->actingAs($user, 'api')
            ->getJson("/api/sidebars/{$sidebar->id}/sidebar-widgets/{$sidebarWidget->id}/roles");

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_store_assigns_role_to_sidebar_widget()
    {
        $user = User::factory()->create();
        $sidebar = Sidebar::factory()->create();
        $sidebarWidget = SidebarWidget::factory()->create(['sidebar_id' => $sidebar->id]);
        $role = Role::factory()->create();

        $response = $this->actingAs($user, 'api')
            ->postJson("/api/sidebars/{$sidebar->id}/sidebar-widgets/{$sidebarWidget->id}/roles/{$role->id}");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Role assigned to sidebar widget.']);
        $this->assertDatabaseHas('role_sidebar_widget', [
            'sidebar_widget_id' => $sidebarWidget->id,
            'role_id' => $role->id,
        ]);
    }

    public function test_destroy_removes_role_from_sidebar_widget()
    {
        $user = User::factory()->create();
        $sidebar = Sidebar::factory()->create();
        $sidebarWidget = SidebarWidget::factory()->create(['sidebar_id' => $sidebar->id]);
        $role = Role::factory()->create();
        $sidebarWidget->roles()->attach($role->id);


        $response = $this->actingAs($user, 'api')
            ->deleteJson("/api/sidebars/{$sidebar->id}/sidebar-widgets/{$sidebarWidget->id}/roles/{$role->id}");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Role removed from sidebar widget.']);
        $this->assertDatabaseMissing('role_sidebar_widget', [
            'sidebar_widget_id' => $sidebarWidget->id,
            'role_id' => $role->id,
        ]);
    }
}