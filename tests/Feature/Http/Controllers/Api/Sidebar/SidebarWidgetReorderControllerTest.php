<?php

namespace Tests\Feature\Api\Sidebar;

use App\Enums\SiteStatus;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\SidebarWidget;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $sidebar = Sidebar::factory()->create(['site_id' => $user->site_id]);
        $sidebarWidget = SidebarWidget::factory()->create(['sidebar_id' => $sidebar->id]);

        // Act
        $response = $this->postJson(route('api.sidebar.widgets.reorder', [$sidebar, $sidebarWidget]), [
            'direction' => 'up',
        ]);

        // Assert
        $response->assertOk()
            ->assertJson([
                'message' => 'Sidebar widget moved up',
            ]);

        $this->assertDatabaseHas('sidebar_widgets', [
            'id' => $sidebarWidget->id,
            // Add assertions for order/position changes if applicable in your logic
        ]);
    }

    
    public function test_it_requires_authentication(): void
    {
        // Arrange
        $sidebar = Sidebar::factory()->create();
        $sidebarWidget = SidebarWidget::factory()->create();

        // Act
        $response = $this->postJson(route('api.sidebar.widgets.reorder', [$sidebar, $sidebarWidget]), [
            'direction' => 'up',
        ]);

        // Assert
        $response->assertUnauthorized();
    }

    
    public function test_it_validates_the_direction_parameter(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $sidebar = Sidebar::factory()->create(['site_id' => $user->site_id]);
        $sidebarWidget = SidebarWidget::factory()->create(['sidebar_id' => $sidebar->id]);

        // Act
        $response = $this->postJson(route('api.sidebar.widgets.reorder', [$sidebar, $sidebarWidget]), [
            'direction' => 'invalid',
        ]);

        // Assert
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['direction']);
    }

    
    public function test_it_returns_404_if_sidebar_not_found(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $sidebarWidget = SidebarWidget::factory()->create();

        // Act
        $response = $this->postJson(route('api.sidebar.widgets.reorder', [999, $sidebarWidget]), [
            'direction' => 'up',
        ]);

        // Assert
        $response->assertNotFound();
    }

    
    public function test_it_returns_404_if_sidebar_widget_not_found(): void
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $sidebar = Sidebar::factory()->create(['site_id' => $user->site_id]);

        // Act
        $response = $this->postJson(route('api.sidebar.widgets.reorder', [$sidebar, 999]), [
            'direction' => 'up',
        ]);

        // Assert
        $response->assertNotFound();
    }

    
    public function test_it_requires_correct_site_id_for_sidebar(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $sidebar = Sidebar::factory()->create(['site_id' => 999]); // Different site ID
        $sidebarWidget = SidebarWidget::factory()->create(['sidebar_id' => $sidebar->id]);

        $response = $this->postJson(route('api.sidebar.widgets.reorder', [$sidebar, $sidebarWidget]), [
            'direction' => 'up',
        ]);

        $response->assertNotFound();
    }
}