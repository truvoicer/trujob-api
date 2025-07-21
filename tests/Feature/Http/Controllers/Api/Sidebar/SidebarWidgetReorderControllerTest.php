<?php

namespace Tests\Feature\Api\Sidebar;

use App\Models\Sidebar;
use App\Models\SidebarWidget;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SidebarWidgetReorderControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_move_a_sidebar_widget(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

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

    /** @test */
    public function it_requires_authentication(): void
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

    /** @test */
    public function it_validates_the_direction_parameter(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

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

    /** @test */
    public function it_returns_404_if_sidebar_not_found(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $sidebarWidget = SidebarWidget::factory()->create();

        // Act
        $response = $this->postJson(route('api.sidebar.widgets.reorder', [999, $sidebarWidget]), [
            'direction' => 'up',
        ]);

        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function it_returns_404_if_sidebar_widget_not_found(): void
    {
        // Arrange
        $user = User::factory()->create();
        $this->actingAs($user);

        $sidebar = Sidebar::factory()->create(['site_id' => $user->site_id]);

        // Act
        $response = $this->postJson(route('api.sidebar.widgets.reorder', [$sidebar, 999]), [
            'direction' => 'up',
        ]);

        // Assert
        $response->assertNotFound();
    }

    /** @test */
    public function it_requires_correct_site_id_for_sidebar(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $sidebar = Sidebar::factory()->create(['site_id' => 999]); // Different site ID
        $sidebarWidget = SidebarWidget::factory()->create(['sidebar_id' => $sidebar->id]);

        $response = $this->postJson(route('api.sidebar.widgets.reorder', [$sidebar, $sidebarWidget]), [
            'direction' => 'up',
        ]);

        $response->assertNotFound();
    }
}