<?php

namespace Tests\Feature;


use App\Enums\SiteStatus;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\Widget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WidgetControllerTest extends TestCase
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

    public function test_it_can_list_widgets()
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        $widgets = Widget::factory()->count(3)->create(['site_id' => $this->site->id]);

        $this
            ->getJson(route('widget.index'))
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }


    public function test_it_can_show_a_widget()
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        $widget = Widget::factory()->create(['site_id' => $this->site->id]);

        $this
            ->getJson(route('widget.show', $widget))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                ],
            ]);
    }


    public function test_it_can_store_a_widget()
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        $widgetData = [
            'title' => $this->faker->sentence(3),
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
        ];

        $this
            ->postJson(route('widget.store'), $widgetData)
            ->assertStatus(200)
            ->assertJson(['message' => 'Widget created']);

        $this->assertDatabaseHas('widgets', $widgetData);
    }


    public function test_it_can_update_a_widget()
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        $widget = Widget::factory()->create(['site_id' => $this->site->id]);
        $updatedWidgetData = [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ];

        $this
            ->patchJson(route('widget.update', $widget), $updatedWidgetData)
            ->assertStatus(200)
            ->assertJson(['message' => 'Widget updated']);

        $this->assertDatabaseHas('widgets', [
            'id' => $widget->id,
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ]);
    }


    public function test_it_can_destroy_a_widget()
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        $widget = Widget::factory()->create(['site_id' => $this->site->id]);

        $this
            ->deleteJson(route('widget.destroy', $widget))
            ->assertStatus(200)
            ->assertJson(['message' => 'Widget deleted']);

        $this->assertDatabaseMissing('widgets', ['id' => $widget->id]);
    }


    public function test_it_returns_unprocessable_entity_on_store_failure()
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        $widgetData = [
            'name' => '', // Invalid data to trigger validation error
            'description' => $this->faker->sentence,
        ];

        $response = $this
            ->postJson(route('widget.store'), $widgetData);

        $response->assertStatus(422);
    }


    public function test_it_returns_unprocessable_entity_on_update_failure()
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        $widget = Widget::factory()->create(['site_id' => $this->site->id]);
        $updatedWidgetData = [
            'name' => '', // Invalid data to trigger validation error
            'description' => $this->faker->sentence,
        ];

        $response = $this
            ->patchJson(route('widget.update', $widget), $updatedWidgetData);

        $response->assertStatus(422);
    }
}
