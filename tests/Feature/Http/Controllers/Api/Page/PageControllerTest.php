<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Site;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PageControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    
    public function it_can_list_pages()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);
        $pages = Page::factory(3)->create(['site_id' => $site->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson(route('pages.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    
    public function it_can_show_a_page()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);
        $page = Page::factory()->create(['site_id' => $site->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson(route('pages.show', $page));

        $response->assertStatus(200);
        $response->assertJson(['data' => ['id' => $page->id]]);
    }

    
    public function it_can_create_a_page()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user, ['*']);

        $data = [
            'title' => $this->faker->sentence,
            'slug' => $this->faker->slug,
            'content' => $this->faker->paragraph,
        ];

        $response = $this->postJson(route('pages.store'), $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('pages', $data);
    }

     
    public function it_returns_error_if_page_creation_fails()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);

        Sanctum::actingAs($user, ['*']);

        $data = [
            'title' => null, // Will cause validation error
            'slug' => $this->faker->slug,
            'content' => $this->faker->paragraph,
        ];

        $response = $this->postJson(route('pages.store'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('title');
    }

    
    public function it_can_update_a_page()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);
        $page = Page::factory()->create(['site_id' => $site->id]);

        Sanctum::actingAs($user, ['*']);

        $data = [
            'title' => $this->faker->sentence,
            'slug' => $this->faker->slug,
            'content' => $this->faker->paragraph,
        ];

        $response = $this->putJson(route('pages.update', $page), $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('pages', $data);
    }

    
    public function it_can_delete_a_page()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);
        $page = Page::factory()->create(['site_id' => $site->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->deleteJson(route('pages.destroy', $page));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }
}