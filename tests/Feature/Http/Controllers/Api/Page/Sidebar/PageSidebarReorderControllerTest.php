<?php

namespace Tests\Feature\Api\Page\Sidebar;

use App\Models\Page;
use App\Models\Sidebar;

use App\Enums\SiteStatus;
use App\Models\PageSidebar;
use App\Models\Role;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PageSidebarReorderControllerTest extends TestCase
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

    public function test_it_can_reorder_a_sidebar_in_the_page() {
        Sanctum::actingAs($this->siteUser, ['*']);
        $page = Page::factory()
            ->has(
                Sidebar::factory()
                    ->state([
                        'site_id' => $this->site->id,
                    ])
                    ->count(2),
            )
            ->create([
                'site_id' => $this->site->id,
            ]);

        $sidebar1 = $page->sidebars->first();
        $sidebar2 = $page->sidebars->last();

        $response = $this
            ->patchJson(route('page.sidebar.reorder.update', [
                'page' => $page->id,
                'sidebar' => $sidebar2->id
            ]), [
                'direction' => 'up',
            ]);

        $response->assertOk()
            ->assertJson([
                'message' => "Sidebar moved up",
            ]);
        // dd(PageSidebar::all()->toArray());
        $this->assertDatabaseHas('page_sidebars', [
            'sidebar_id' => $sidebar2->id,
            'order' => 0,
        ]);

        $this->assertDatabaseHas('page_sidebars', [
            'sidebar_id' => $sidebar1->id,
            'order' => 1,
        ]);
    }


    public function test_it_requires_a_valid_direction()
    {

        Sanctum::actingAs($this->siteUser, ['*']);
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $sidebar = Sidebar::factory()->create([
            'site_id' => $this->site->id,
        ]);

        $response = $this
            ->patchJson(route('page.sidebar.reorder.update', ['page' => $page->id, 'sidebar' => $sidebar->id]), [
                'direction' => 'invalid',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['direction']);
    }


    public function test_it_returns_a_403_if_the_user_is_not_authenticated()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $sidebar = Sidebar::factory()->create([
            'site_id' => $this->site->id,
        ]);

        $response = $this->patchJson(route('page.sidebar.reorder.update', ['page' => $page->id, 'sidebar' => $sidebar->id]), [
            'direction' => 'up',
        ]);

        $response->assertStatus(401);
    }


    public function test_it_returns_a_404_if_the_page_is_not_found()
    {

        Sanctum::actingAs($this->siteUser, ['*']);
        $sidebar = Sidebar::factory()->create([
            'site_id' => $this->site->id,
        ]);

        $response = $this
            ->patchJson(route('page.sidebar.reorder.update', ['page' => 999, 'sidebar' => $sidebar->id]), [
                'direction' => 'up',
            ]);

        $response->assertStatus(404);
    }
}
