<?php

namespace Tests\Feature;

use App\Models\Site;
use App\Models\User;
use App\Models\Widget;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class WidgetControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
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
