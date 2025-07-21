<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\Sidebar;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSidebarControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create();
        $sidebars = Sidebar::factory()->count(3)->create();
        $page->sidebars()->attach($sidebars->pluck('id')->toArray());

        $response = $this->actingAs($user)
            ->getJson("/api/pages/{$page->id}/sidebars");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(3, 'data');
    }

    public function testStore()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create();
        $sidebar = Sidebar::factory()->create();

        $response = $this->actingAs($user)
            ->postJson("/api/pages/{$page->id}/sidebars/{$sidebar->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Sidebar created',
            ]);

        $this->assertDatabaseHas('page_sidebar', [
            'page_id' => $page->id,
            'sidebar_id' => $sidebar->id,
        ]);
    }

    public function testDestroy()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create();
        $sidebar = Sidebar::factory()->create();
        $page->sidebars()->attach($sidebar->id);

        $response = $this->actingAs($user)
            ->deleteJson("/api/pages/{$page->id}/sidebars/{$sidebar->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Sidebar deleted',
            ]);

        $this->assertDatabaseMissing('page_sidebar', [
            'page_id' => $page->id,
            'sidebar_id' => $sidebar->id,
        ]);
    }

    public function testIndexWithSortingAndPagination()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create();
        $sidebars = Sidebar::factory()->count(5)->create();
        $page->sidebars()->attach($sidebars->pluck('id')->toArray());

        $response = $this->actingAs($user)
            ->getJson("/api/pages/{$page->id}/sidebars?sort=name&order=asc&per_page=2&page=2");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at',
                    ],
                ],
                'links',
                'meta',
            ])
            ->assertJsonCount(2, 'data');

        $response->assertJsonPath('meta.current_page', 2);
        $response->assertJsonPath('meta.per_page', 2);
    }

}