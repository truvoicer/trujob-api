<?php

namespace Tests\Feature;

use App\Models\Page;

use App\Enums\SiteStatus;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PageControllerTest extends TestCase
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
    
    public function it_can_list_pages()
    {
        $site = Site::factory()->create([
            'user_id' => $this->user->id,
        ]);
        $pages = Page::factory(3)->create([
            'site_id' => $this->site->id,
        ]);

        Sanctum::actingAs($this->siteUser, ['*']);

        $response = $this->getJson(route('page.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }

    
    public function it_can_show_a_page()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $this->user->id]);
        $page = Page::factory()->create(['site_id' => $site->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->getJson(route('page.show', $page));

        $response->assertStatus(200);
        $response->assertJson(['data' => ['id' => $page->id]]);
    }

    
    public function it_can_create_a_page()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $this->user->id]);

        Sanctum::actingAs($user, ['*']);

        $data = [
            'title' => $this->faker->sentence,
            'slug' => $this->faker->slug,
            'content' => $this->faker->paragraph,
        ];

        $response = $this->postJson(route('page.store'), $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('pages', $data);
    }

     
    public function it_returns_error_if_page_creation_fails()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $this->user->id]);

        Sanctum::actingAs($user, ['*']);

        $data = [
            'title' => null, // Will cause validation error
            'slug' => $this->faker->slug,
            'content' => $this->faker->paragraph,
        ];

        $response = $this->postJson(route('page.store'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('title');
    }

    
    public function it_can_update_a_page()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $this->user->id]);
        $page = Page::factory()->create(['site_id' => $site->id]);

        Sanctum::actingAs($user, ['*']);

        $data = [
            'title' => $this->faker->sentence,
            'slug' => $this->faker->slug,
            'content' => $this->faker->paragraph,
        ];

        $response = $this->putJson(route('page.update', $page), $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('pages', $data);
    }

    
    public function it_can_delete_a_page()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $this->user->id]);
        $page = Page::factory()->create(['site_id' => $site->id]);

        Sanctum::actingAs($user, ['*']);

        $response = $this->deleteJson(route('page.destroy', $page));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }
}