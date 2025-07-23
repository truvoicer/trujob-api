<?php

namespace Tests\Feature\Api\Page\Sidebar;

use App\Models\Page;
use App\Models\PageBlock;

use App\Enums\SiteStatus;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        Sanctum::actingAs($this->siteUser, ['*']);
    }
    
    public function test_it_can_reorder_a_page_block_in_the_sidebar()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $pageBlock1 = PageBlock::factory()->create(['page_id' => $page->id, 'order' => 1]);
        $pageBlock2 = PageBlock::factory()->create(['page_id' => $page->id, 'order' => 2]);

        $response = $this
            ->postJson(route('page.sidebar.reorder', ['page' => $page->id, 'page_block' => $pageBlock2->id]), [
                'direction' => 'up',
            ]);

        $response->assertOk()
            ->assertJson([
                'message' => "Page block moved up",
            ]);

        $this->assertDatabaseHas('page_blocks', [
            'id' => $pageBlock2->id,
            'order' => 1,
        ]);

        $this->assertDatabaseHas('page_blocks', [
            'id' => $pageBlock1->id,
            'order' => 2,
        ]);
    }

    
    public function test_it_requires_a_valid_direction()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $pageBlock = PageBlock::factory()->create(['page_id' => $page->id]);

        $response = $this
            ->postJson(route('page.sidebar.reorder', ['page' => $page->id, 'page_block' => $pageBlock->id]), [
                'direction' => 'invalid',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['direction']);
    }

    
    public function test_it_returns_a_403_if_the_user_is_not_authenticated()
    {
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $pageBlock = PageBlock::factory()->create(['page_id' => $page->id]);

        $response = $this->postJson(route('page.sidebar.reorder', ['page' => $page->id, 'page_block' => $pageBlock->id]), [
            'direction' => 'up',
        ]);

        $response->assertStatus(401);
    }

    
    public function test_it_returns_a_404_if_the_page_is_not_found()
    {
        $user = User::factory()->create();
        $pageBlock = PageBlock::factory()->create();

        $response = $this
            ->postJson(route('page.sidebar.reorder', ['page' => 999, 'page_block' => $pageBlock->id]), [
                'direction' => 'up',
            ]);

        $response->assertStatus(404);
    }

    
    public function test_it_returns_a_404_if_the_page_block_is_not_found()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);

        $response = $this
            ->postJson(route('page.sidebar.reorder', ['page' => $page->id, 'page_block' => 999]), [
                'direction' => 'up',
            ]);

        $response->assertStatus(404);
    }
}