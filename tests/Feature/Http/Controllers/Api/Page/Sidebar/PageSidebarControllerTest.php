<?php

namespace Tests\Feature;

use App\Enums\SiteStatus;
use App\Models\Page;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageSidebarControllerTest extends TestCase
{
    use RefreshDatabase;

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

    public function testIndex()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create([
            'site_id' => $this->site->id
        ]);
        $sidebars = Sidebar::factory()->count(3)->create([
            'site_id' => $this->site->id
        ]);
        $page->sidebars()->attach($sidebars->pluck('id')->toArray());

        Sanctum::actingAs($this->siteUser, ['*']);
        $response = $this
            ->getJson(route('page.sidebar.index', ['page' => $page->id]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'title',
                        'icon',
                        'properties',
                        'roles',
                        'widgets',
                        'has_permission',
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
        $page = Page::factory()->create([
            'site_id' => $this->site->id
        ]);
        $sidebar = Sidebar::factory()->create([
            'site_id' => $this->site->id
        ]);

        Sanctum::actingAs($this->siteUser, ['*']);
        $response = $this
            ->postJson(route('page.sidebar.store', ['page' => $page->id, 'sidebar' => $sidebar->id]));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Sidebar created',
            ]);

        $this->assertDatabaseHas('page_sidebars', [
            'page_id' => $page->id,
            'sidebar_id' => $sidebar->id,
        ]);
    }

    public function testDestroy()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create([
            'site_id' => $this->site->id
        ]);
        $sidebar = Sidebar::factory()->create([
            'site_id' => $this->site->id
        ]);
        $page->sidebars()->attach($sidebar->id);

        Sanctum::actingAs($this->siteUser, ['*']);
        $response = $this
            ->deleteJson(route('page.sidebar.destroy', ['page' => $page->id, 'sidebar' => $sidebar->id]));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Sidebar deleted',
            ]);

        $this->assertDatabaseMissing('page_sidebars', [
            'page_id' => $page->id,
            'sidebar_id' => $sidebar->id,
        ]);
    }

    // public function testIndexWithSortingAndPagination()
    // {
    //     $user = User::factory()->create();
    //     $page = Page::factory()->create([
    //         'site_id' => $this->site->id
    //     ]);
    //     $sidebars = Sidebar::factory()->count(5)->create([
    //         'site_id' => $this->site->id
    //     ]);
    //     $page->sidebars()->attach($sidebars->pluck('id')->toArray());
    //     Sanctum::actingAs($this->siteUser, ['*']);
    //     $response = $this
    //         ->getJson(route('page.sidebar.index', ['page' => $page->id, 'sort' => 'name', 'order' => 'asc', 'per_page' => 2, 'page' => 2]));

    //     $response->assertStatus(200)
    //         ->assertJsonStructure([
    //             'data' => [
    //                 '*' => [
    //                     'id',
    //                     'name',
    //                     'created_at',
    //                     'updated_at',
    //                 ],
    //             ],
    //             'links',
    //             'meta',
    //         ])
    //         ->assertJsonCount(2, 'data');

    //     $response->assertJsonPath('meta.current_page', 2);
    //     $response->assertJsonPath('meta.per_page', 2);
    // }

}