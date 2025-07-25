<?php

namespace Tests\Feature\Api\Sidebar;

use App\Enums\SiteStatus;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\SidebarWidget;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class SidebarWidgetReorderControllerTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_it_can_move_a_sidebar_widget(): void
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

        $sidebarWidget1 = SidebarWidget::first();
        $sidebarWidget2 = SidebarWidget::find(2);

        // Act
        $response = $this->patchJson(route('sidebar.widget.rel.reorder.update', [
            $sidebar, $sidebarWidget2]), [
            'direction' => 'up',
        ]);

        // Assert
        $response->assertOk()
            ->assertJson([
                'message' => 'Sidebar widget moved up',
            ]);

        $this->assertDatabaseHas('sidebar_widgets', [
            'id' => $sidebarWidget2->id,
            'order' => 0,
        ]);
        $this->assertDatabaseHas('sidebar_widgets', [
            'id' => $sidebarWidget1->id,
            'order' => 1,
        ]);
    }


    public function test_it_requires_authentication(): void
    {
        $user = User::factory()->create();
        // Arrange
        Sanctum::actingAs($user, ['*']);

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

        $sidebarWidget1 = SidebarWidget::first();
        $sidebarWidget2 = SidebarWidget::find(2);
        // Act
        $response = $this->patchJson(route('sidebar.widget.rel.reorder.update', [$sidebar, $sidebarWidget2]), [
            'direction' => 'up',
        ]);

        // Assert
        $response->assertUnauthorized();
    }


    public function test_it_validates_the_direction_parameter(): void
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

        $sidebarWidget1 = SidebarWidget::first();
        $sidebarWidget2 = SidebarWidget::find(2);

        // Act
        $response = $this->patchJson(route('sidebar.widget.rel.reorder.update', [$sidebar, $sidebarWidget2]), [
            'direction' => 'invalid',
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['direction']);
    }


    public function test_it_returns_404_if_sidebar_not_found(): void
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

        $sidebarWidget1 = SidebarWidget::first();
        $sidebarWidget2 = SidebarWidget::find(2);

        // Act
        $response = $this->patchJson(route('sidebar.widget.rel.reorder.update', [999, $sidebarWidget2]), [
            'direction' => 'up',
        ]);

        // Assert
        $response->assertNotFound();
    }


    public function test_it_returns_404_if_sidebar_widget_not_found(): void
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

        $sidebarWidget1 = SidebarWidget::first();
        $sidebarWidget2 = SidebarWidget::find(2);

        // Act
        $response = $this->patchJson(route('sidebar.widget.rel.reorder.update', [$sidebar, 999]), [
            'direction' => 'up',
        ]);

        // Assert
        $response->assertNotFound();
    }
}
