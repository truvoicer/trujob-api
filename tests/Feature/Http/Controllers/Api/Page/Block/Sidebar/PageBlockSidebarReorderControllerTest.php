<?php

namespace Tests\Feature\Api\Page\Block\Sidebar;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\PageBlockSidebar;

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

class PageBlockSidebarReorderControllerTest extends TestCase
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
    
    public function it_can_reorder_a_page_block_sidebar(): void
    {
        // Arrange
        $user = User::factory()->create();
        $page = Page::factory()->create(['site_id' => $user->site_id]);
        $pageBlock = PageBlock::factory()->create(['page_id' => $page->id]);

        // Create two page block sidebars for reordering.
        $pageBlockSidebar1 = PageBlockSidebar::factory()->create([
            'page_block_id' => $pageBlock->id,
            'order' => 1,
        ]);
        $pageBlockSidebar2 = PageBlockSidebar::factory()->create([
            'page_block_id' => $pageBlock->id,
            'order' => 2,
        ]);

        Sanctum::actingAs($user, ['*']);

        // Act
        $response = $this->postJson(
            route('page.blocks.sidebars.reorder', [
                'page' => $page->id,
                'page_block' => $pageBlock->id,
                'page_block_sidebar' => $pageBlockSidebar1->id,
            ]),
            ['direction' => 'down']
        );

        // Assert
        $response->assertOk()
            ->assertJson([
                'message' => 'Page block moved down',
            ]);

        // Verify that the orders have been updated in the database.
        $this->assertEquals(2, $pageBlockSidebar1->fresh()->order);
        $this->assertEquals(1, $pageBlockSidebar2->fresh()->order);
    }

    
    public function it_returns_404_if_page_does_not_exist(): void
    {
        // Arrange
        $user = User::factory()->create();
        $pageBlock = PageBlock::factory()->create();
        $pageBlockSidebar = PageBlockSidebar::factory()->create();

        Sanctum::actingAs($user, ['*']);

        // Act
        $response = $this->postJson(
            route('page.blocks.sidebars.reorder', [
                'page' => 999,
                'page_block' => $pageBlock->id,
                'page_block_sidebar' => $pageBlockSidebar->id,
            ]),
            ['direction' => 'down']
        );

        // Assert
        $response->assertNotFound();
    }

    
    public function it_returns_404_if_page_block_does_not_exist(): void
    {
        // Arrange
        $user = User::factory()->create();
        $page = Page::factory()->create(['site_id' => $user->site_id]);
        $pageBlockSidebar = PageBlockSidebar::factory()->create();

        Sanctum::actingAs($user, ['*']);

        // Act
        $response = $this->postJson(
            route('page.blocks.sidebars.reorder', [
                'page' => $page->id,
                'page_block' => 999,
                'page_block_sidebar' => $pageBlockSidebar->id,
            ]),
            ['direction' => 'down']
        );

        // Assert
        $response->assertNotFound();
    }

        
    public function it_returns_404_if_page_block_sidebar_does_not_exist(): void
    {
        // Arrange
        $user = User::factory()->create();
        $page = Page::factory()->create(['site_id' => $user->site_id]);
        $pageBlock = PageBlock::factory()->create(['page_id' => $page->id]);

        Sanctum::actingAs($user, ['*']);

        // Act
        $response = $this->postJson(
            route('page.blocks.sidebars.reorder', [
                'page' => $page->id,
                'page_block' => $pageBlock->id,
                'page_block_sidebar' => 999,
            ]),
            ['direction' => 'down']
        );

        // Assert
        $response->assertNotFound();
    }

    
    public function it_requires_a_direction(): void
    {
        // Arrange
        $user = User::factory()->create();
        $page = Page::factory()->create(['site_id' => $user->site_id]);
        $pageBlock = PageBlock::factory()->create(['page_id' => $page->id]);
        $pageBlockSidebar = PageBlockSidebar::factory()->create([
            'page_block_id' => $pageBlock->id,
            'order' => 1,
        ]);
        Sanctum::actingAs($user, ['*']);

        // Act
        $response = $this->postJson(
            route('page.blocks.sidebars.reorder', [
                'page' => $page->id,
                'page_block' => $pageBlock->id,
                'page_block_sidebar' => $pageBlockSidebar->id,
            ]),
            []
        );

        // Assert
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['direction']);
    }

    
    public function it_requires_the_direction_to_be_a_valid_value(): void
    {
        // Arrange
        $user = User::factory()->create();
        $page = Page::factory()->create(['site_id' => $user->site_id]);
        $pageBlock = PageBlock::factory()->create(['page_id' => $page->id]);
        $pageBlockSidebar = PageBlockSidebar::factory()->create([
            'page_block_id' => $pageBlock->id,
            'order' => 1,
        ]);
        Sanctum::actingAs($user, ['*']);

        // Act
        $response = $this->postJson(
            route('page.blocks.sidebars.reorder', [
                'page' => $page->id,
                'page_block' => $pageBlock->id,
                'page_block_sidebar' => $pageBlockSidebar->id,
            ]),
            ['direction' => 'invalid']
        );

        // Assert
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['direction']);
    }
}