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
        Sanctum::actingAs($this->siteUser, ['*']);
    }
    public function testIndex(): void
    {
        $user = User::factory()->create();
        $sidebar = Sidebar::factory()->create();
        SidebarWidget::factory()->count(3)->create(['sidebar_id' => $sidebar->id]);

        $response = $this
            ->getJson(route('sidebar.widget.index', ['sidebar' => $sidebar->id]));

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

        $response = $this->getJson(route('sidebar.widget.relshow', ['sidebar' => $sidebar->id, 'sidebarWidget' => $sidebarWidget->id]));

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
        $widget = Widget::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $sidebar = Sidebar::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $sidebarWidget = SidebarWidget::factory()->create(['sidebar_id' => $sidebar->id,
            'widget_id' => $widget->id,
        ]);

        $data = [
            'order' => 1
        ];

        $response = $this->patchJson(route('sidebar.widget.relupdate', ['sidebar' => $sidebar->id, 'sidebarWidget' => $sidebarWidget->id]), $data);

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Sidebar widget updated']);

        $this->assertDatabaseHas('sidebar_widgets', [
            'id' => $sidebarWidget->widget->id,
            'order' => 1,
        ]);
    }

    public function testUpdateValidationError(): void
    {
        $widget = Widget::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $sidebar = Sidebar::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $sidebarWidget = SidebarWidget::factory()->create([
            'sidebar_id' => $sidebar->id,
            'widget_id' => $widget->id,
        ]);

        $data = [
            'roles' => 'invalid',
        ];

        $response = $this->patchJson(route('sidebar.widget.relupdate', ['sidebar' => $sidebar->id, 'sidebarWidget' => $sidebarWidget->id]), $data);
        $response->assertStatus(422);
    }

    public function testDestroy(): void
    {
        $sidebar = Sidebar::factory()->create([
            'site_id' => $this->site->id,
        ])->first();
        $widget = Widget::factory()->create([
            'site_id' => $this->site->id,
        ])->first();
        $sidebarWidget = SidebarWidget::factory()->create([
            'sidebar_id' => $sidebar->id,
            'widget_id' => $widget->id,
        ]);

        $response = $this->deleteJson(route('sidebar.widget.reldestroy', ['sidebar' => $sidebar->id, 'sidebarWidget' => $sidebarWidget->id]));

        $response->assertStatus(200);
        $response->assertJson(['message' => 'Sidebar widget deleted']);

        $this->assertDatabaseMissing('sidebar_widgets', [
            'id' => $sidebarWidget->id,
        ]);
    }
}
