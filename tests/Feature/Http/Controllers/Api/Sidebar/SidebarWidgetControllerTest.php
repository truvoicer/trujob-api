<?php

namespace Tests\Feature;

use App\Models\Sidebar;
use App\Models\SidebarWidget;
use App\Models\User;
use App\Models\Widget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SidebarWidgetControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testIndex(): void
    {
        $user = User::factory()->create();
        $sidebar = Sidebar::factory()->create();
        SidebarWidget::factory()->count(3)->create(['sidebar_id' => $sidebar->id]);

        $response = $this->actingAs($user)->getJson("/api/sidebars/{$sidebar->id}/sidebar-widgets");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'sidebar_id',
                    'widget_id',
                    'order',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function testShow(): void
    {
        $user = User::factory()->create();
        $sidebar = Sidebar::factory()->create();
        $sidebarWidget = SidebarWidget::factory()->create(['sidebar_id' => $sidebar->id]);

        $response = $this->actingAs($user)->getJson("/api/sidebars/{$sidebar->id}/sidebar-widgets/{$sidebarWidget->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'sidebar_id',
                'widget_id',
                'order',
                'created_at',
                'updated_at',
            ],
        ]);
    }

    public function testStore(): void
    {
        $user = User::factory()->create();
        $sidebar = Sidebar::factory()->create();
        $widget = Widget::factory()->create();

        $data = [
            'order' => $this->faker->numberBetween(1, 10),
        ];

        $response = $this->actingAs($user)->postJson("/api/sidebars/{$sidebar->id}/widgets/{$widget->id}/sidebar-widgets", $data);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Sidebar widget created']);

        $this->assertDatabaseHas('sidebar_widgets', [
            'sidebar_id' => $sidebar->id,
            'widget_id' => $widget->id,
            'order' => $data['order'],
        ]);
    }

    public function testStoreValidationError(): void
    {
        $user = User::factory()->create();
        $sidebar = Sidebar::factory()->create();
        $widget = Widget::factory()->create();

        $data = [
            'order' => 'invalid',
        ];

        $response = $this->actingAs($user)->postJson("/api/sidebars/{$sidebar->id}/widgets/{$widget->id}/sidebar-widgets", $data);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'message',
            'errors' => [
                'order'
            ]
        ]);
    }

    public function testUpdate(): void
    {
        $user = User::factory()->create();
        $sidebar = Sidebar::factory()->create();
        $sidebarWidget = SidebarWidget::factory()->create(['sidebar_id' => $sidebar->id]);

        $data = [
            'order' => $this->faker->numberBetween(11, 20),
        ];

        $response = $this->actingAs($user)->putJson("/api/sidebars/{$sidebar->id}/sidebar-widgets/{$sidebarWidget->id}", $data);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Sidebar widget updated']);

        $this->assertDatabaseHas('sidebar_widgets', [
            'id' => $sidebarWidget->id,
            'order' => $data['order'],
        ]);
    }

        public function testUpdateValidationError(): void
    {
        $user = User::factory()->create();
        $sidebar = Sidebar::factory()->create();
        $sidebarWidget = SidebarWidget::factory()->create(['sidebar_id' => $sidebar->id]);

        $data = [
            'order' => 'invalid',
        ];

        $response = $this->actingAs($user)->putJson("/api/sidebars/{$sidebar->id}/sidebar-widgets/{$sidebarWidget->id}", $data);

        $response->assertStatus(422);
    }

    public function testDestroy(): void
    {
        $user = User::factory()->create();
        $sidebar = Sidebar::factory()->create();
        $sidebarWidget = SidebarWidget::factory()->create(['sidebar_id' => $sidebar->id]);

        $response = $this->actingAs($user)->deleteJson("/api/sidebars/{$sidebar->id}/sidebar-widgets/{$sidebarWidget->id}");

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Sidebar widget deleted']);

        $this->assertDatabaseMissing('sidebar_widgets', [
            'id' => $sidebarWidget->id,
        ]);
    }
}