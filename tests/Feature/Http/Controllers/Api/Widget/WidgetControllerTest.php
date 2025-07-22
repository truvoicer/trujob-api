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
        Sanctum::actingAs($this->siteUser, ['*']);
    }
    
    public function it_can_list_widgets()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $user->site_id = $site->id;
        $user->save();
        $widgets = Widget::factory()->count(3)->create(['site_id' => $site->id]);

        $this->actingAs($user, 'api')
            ->getJson(route('widgets.index'))
            ->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    
    public function it_can_show_a_widget()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $user->site_id = $site->id;
        $user->save();
        $widget = Widget::factory()->create(['site_id' => $site->id]);

        $this->actingAs($user, 'api')
            ->getJson(route('widgets.show', $widget))
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'description',
                ],
            ]);
    }

    
    public function it_can_store_a_widget()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $user->site_id = $site->id;
        $user->save();
        $widgetData = [
            'name' => $this->faker->name,
            'description' => $this->faker->sentence,
        ];

        $this->actingAs($user, 'api')
            ->postJson(route('widgets.store'), $widgetData)
            ->assertStatus(200)
            ->assertJson(['message' => 'Widget created']);

        $this->assertDatabaseHas('widgets', $widgetData);
    }

    
    public function it_can_update_a_widget()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $user->site_id = $site->id;
        $user->save();
        $widget = Widget::factory()->create(['site_id' => $site->id]);
        $updatedWidgetData = [
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ];

        $this->actingAs($user, 'api')
            ->putJson(route('widgets.update', $widget), $updatedWidgetData)
            ->assertStatus(200)
            ->assertJson(['message' => 'Widget updated']);

        $this->assertDatabaseHas('widgets', [
            'id' => $widget->id,
            'name' => 'Updated Name',
            'description' => 'Updated Description',
        ]);
    }

    
    public function it_can_destroy_a_widget()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $user->site_id = $site->id;
        $user->save();
        $widget = Widget::factory()->create(['site_id' => $site->id]);

        $this->actingAs($user, 'api')
            ->deleteJson(route('widgets.destroy', $widget))
            ->assertStatus(200)
            ->assertJson(['message' => 'Widget deleted']);

        $this->assertDatabaseMissing('widgets', ['id' => $widget->id]);
    }

    
    public function it_returns_unprocessable_entity_on_store_failure()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $user->site_id = $site->id;
        $user->save();
        $widgetData = [
            'name' => '', // Invalid data to trigger validation error
            'description' => $this->faker->sentence,
        ];

        $response = $this->actingAs($user, 'api')
            ->postJson(route('widgets.store'), $widgetData);

        $response->assertStatus(422);
    }

    
    public function it_returns_unprocessable_entity_on_update_failure()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create();
        $user->site_id = $site->id;
        $user->save();
        $widget = Widget::factory()->create(['site_id' => $site->id]);
        $updatedWidgetData = [
            'name' => '', // Invalid data to trigger validation error
            'description' => $this->faker->sentence,
        ];

        $response = $this->actingAs($user, 'api')
            ->putJson(route('widgets.update', $widget), $updatedWidgetData);

        $response->assertStatus(422);
    }
}
