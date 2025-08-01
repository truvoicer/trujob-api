<?php

namespace Tests\Feature;

use App\Models\Page;

use App\Enums\SiteStatus;
use App\Enums\ViewType;
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
    }

    public function test_it_can_list_pages()
    {
        $pages = Page::factory(3)->create([
            'site_id' => $this->site->id,
        ]);


        Sanctum::actingAs($this->siteUser, ['*']);

        $response = $this->getJson(route('page.index'));

        $response->assertStatus(200);
        $response->assertJsonCount(3, 'data');
    }


    public function test_it_can_show_a_page()
    {
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);


        Sanctum::actingAs($this->siteUser, ['*']);

        $response = $this->getJson(route('page.show', $page));

        $response->assertStatus(200);
        $response->assertJson(['data' => ['id' => $page->id]]);
    }


    public function test_it_can_create_a_page()
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        $data = [
            'view' => ViewType::AdminPage->value,
            'name' => $this->faker->slug,
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraph,
        ];

        $response = $this->postJson(route('page.store'), $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('pages', $data);
    }


    public function test_it_returns_error_if_page_creation_fails()
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        $data = [
            'title' => null, // Will cause validation error
            'slug' => $this->faker->slug,
            'content' => $this->faker->paragraph,
        ];

        $response = $this->postJson(route('page.store'), $data);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('title');
    }


    public function test_it_can_update_a_page()
    {
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);


        Sanctum::actingAs($this->siteUser, ['*']);

        $data = [
            'title' => $this->faker->sentence,
            'name' => $this->faker->slug,
            'content' => $this->faker->paragraph,
        ];

        $response = $this->patchJson(route('page.update', $page), $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('pages', $data);
    }


    public function test_it_can_delete_a_page()
    {
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);

        Sanctum::actingAs($this->siteUser, ['*']);

        $response = $this->deleteJson(route('page.destroy', $page));

        $response->assertStatus(200);
        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }
}
