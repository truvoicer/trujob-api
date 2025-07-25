<?php

namespace Tests\Feature;

use App\Enums\SiteStatus;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\SidebarWidget;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\Widget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SidebarWidgetControllerTest extends TestCase
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
    public function testIndex(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $sidebar = Sidebar::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $widget = Widget::factory()->create([
            'site_id' => $this->site->id,
        ]);

        $response = $this
            ->getJson(route('sidebar.widget.index', ['sidebar' => $sidebar->id]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'title',
                    'icon',
                    'properties',
                    'order',
                    'has_container',
                    'roles',
                    'has_permission',
                ],
            ],
        ]);
    }

    public function testShow(): void
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

        $response = $this->getJson(route('sidebar.widget.rel.show', ['sidebar' => $sidebar->id, 'sidebarWidget' => $sidebarWidget->id]));

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'name',
                'title',
                'icon',
                'properties',
                'order',
                'has_container',
                'roles',
                'has_permission',
            ],
        ]);
    }

    public function testStore(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);
        $sidebar = Sidebar::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $widget = Widget::factory()->create([
            'site_id' => $this->site->id,
        ]);

        $data = [
            'order' => $this->faker->numberBetween(1, 10),
        ];

        $response = $this->postJson(route('sidebar.widget.store', ['sidebar' => $sidebar->id, 'widget' => $widget->id]), $data);

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
        Sanctum::actingAs($this->siteUser, ['*']);

        $widget = Widget::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $sidebar = Sidebar::factory()->create([
            'site_id' => $this->site->id,
        ]);

        $data = [
            'order' => 'invalid',
        ];

        $response = $this->postJson(route('sidebar.widget.store', ['sidebar' => $sidebar->id, 'widget' => $widget->id]), $data);

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

        $data = [
            'order' => $this->faker->numberBetween(11, 20),
        ];

        $response = $this->patchJson(route('sidebar.widget.rel.update', ['sidebar' => $sidebar->id, 'sidebarWidget' => $sidebarWidget->id]), $data);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Sidebar widget updated']);

        $this->assertDatabaseHas('sidebar_widgets', [
            'id' => $sidebarWidget->id,
            'order' => $data['order'],
        ]);
    }

    public function testUpdateValidationError(): void
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

        $data = [
            'order' => 'invalid',
        ];

        $response = $this->patchJson(route('sidebar.widget.rel.update', ['sidebar' => $sidebar->id, 'sidebarWidget' => $sidebarWidget->id]), $data);

        $response->assertStatus(422);
    }

    public function testDestroy(): void
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

        $response = $this->deleteJson(route('sidebar.widget.rel.destroy', ['sidebar' => $sidebar->id, 'sidebarWidget' => $sidebarWidget->id]));

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Sidebar widget deleted']);

        $this->assertDatabaseMissing('sidebar_widgets', [
            'id' => $sidebarWidget->id,
        ]);
    }
}
