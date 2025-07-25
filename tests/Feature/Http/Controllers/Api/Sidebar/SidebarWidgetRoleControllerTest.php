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
        // Arrange
        Sanctum::actingAs($this->siteUser, ['*']);

        $sidebar = Sidebar::factory()
            ->has(
                Widget::factory()
                    ->state([
                        'site_id' => $this->site->id,
                    ])
                ->count(2),
            )
            ->create([
                'site_id' => $this->site->id
            ]);

        $sidebarWidget = SidebarWidget::first();
        $sidebarWidget2 = SidebarWidget::find(2);
        $roles = Role::factory()->count(3)->create();

        $sidebarWidget->roles()->attach($roles->pluck('id')->toArray());

        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );
        $response = $this
            ->getJson(route('sidebar.widget.rel.role.index', [$sidebar->id, $sidebarWidget->id]));

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    public function test_store_assigns_role_to_sidebar_widget()
    {
        // Arrange
        Sanctum::actingAs($this->siteUser, ['*']);

        $sidebar = Sidebar::factory()
            ->has(
                Widget::factory()
                    ->state([
                        'site_id' => $this->site->id,
                    ])
                ->count(2),
            )
            ->create([
                'site_id' => $this->site->id
            ]);

        $sidebarWidget = SidebarWidget::first();
        $sidebarWidget2 = SidebarWidget::find(2);
        $role = Role::factory()->create();

        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );
        $response = $this
            ->postJson(
                route(
                    'sidebar.widget.rel.role.store',
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

        // Arrange
        Sanctum::actingAs($this->siteUser, ['*']);

        $sidebar = Sidebar::factory()
            ->has(
                Widget::factory()
                    ->state([
                        'site_id' => $this->site->id,
                    ])
                ->count(2),
            )
            ->create([
                'site_id' => $this->site->id
            ]);

        $sidebarWidget = SidebarWidget::first();
        $sidebarWidget2 = SidebarWidget::find(2);

        $role = Role::factory()->create();
        $sidebarWidget->roles()->attach($role->id);


        Sanctum::actingAs(
            $this->siteUser,
            ['*']
        );
        $response = $this
            ->deleteJson(
                route(
                    'sidebar.widget.rel.role.destroy',
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
