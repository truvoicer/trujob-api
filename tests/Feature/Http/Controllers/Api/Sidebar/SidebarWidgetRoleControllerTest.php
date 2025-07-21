<?php

namespace Tests\Feature;

use App\Enums\SiteStatus;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\SidebarWidget;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SidebarWidgetRoleControllerTest extends TestCase
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

    public function test_index_returns_roles_for_sidebar_widget()
    {
        $sidebar = Sidebar::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $widget = Widget::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $sidebarWidget = SidebarWidget::factory()->create([
            'sidebar_id' => $sidebar->id,
            'widget_id' => $widget->id,
        ]);
        $roles = Role::factory()->count(3)->create();

        $sidebarWidget->roles()->attach($roles->pluck('id')->toArray());

        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );
        $response = $this
            ->getJson(route('sidebar.widget.relrole.index', [$sidebar->id, $sidebarWidget->id]));

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_store_assigns_role_to_sidebar_widget()
    {
        $sidebar = Sidebar::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $widget = Widget::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $sidebarWidget = SidebarWidget::factory()->create([
            'sidebar_id' => $sidebar->id,
            'widget_id' => $widget->id,
        ]);
        $role = Role::factory()->create();

        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );
        $response = $this
            ->postJson(
                route(
                    'sidebar.widget.relrole.store',
                    [$sidebar->id, $sidebarWidget->id, $role->id]
                )
            );


        $response->assertStatus(200);
        $response->assertJson(['message' => 'Role assigned to sidebar widget.']);
        $this->assertDatabaseHas('sidebar_widget_roles', [
            'sidebar_widget_id' => $sidebarWidget->id,
            'role_id' => $role->id,
        ]);
    }

    public function test_destroy_removes_role_from_sidebar_widget()
    {

        $sidebar = Sidebar::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $widget = Widget::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $sidebarWidget = SidebarWidget::factory()->create([
            'sidebar_id' => $sidebar->id,
            'widget_id' => $widget->id,
        ]);
        $role = Role::factory()->create();
        $sidebarWidget->roles()->attach($role->id);


        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );
        $response = $this
            ->deleteJson(
                route(
                    'sidebar.widget.relrole.destroy',
                    [$sidebar->id, $sidebarWidget->id, $role->id]
                )
            );

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Role removed from sidebar widget.']);
        $this->assertDatabaseMissing('sidebar_widget_roles', [
            'sidebar_widget_id' => $sidebarWidget->id,
            'role_id' => $role->id,
        ]);
    }
}
