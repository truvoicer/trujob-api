<?php

namespace Tests\Feature\Api\Page\Block\Sidebar;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\PageBlockSidebar;

use App\Enums\SiteStatus;
use App\Models\Block;
use App\Models\Role;
use App\Models\Sidebar;
use App\Models\Site;
use App\Models\SiteUser;
use App\Models\User;
use App\Models\Widget;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
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

    }

    public function test_it_can_reorder_a_page_block_sidebar(): void
    {
        // Arrange
        $pageBlock = PageBlock::factory()
        ->for(
            Page::factory()->create([
                'site_id' => $this->site->id,
            ]), 'page'
        )
        ->for(
            Block::factory()->create(), 'block'
        )
        ->has(
            Sidebar::factory()
            ->state([
                'site_id' => $this->site->id,
            ])
            ->count(2), 'sidebars'
        )
        ->create();
        $pageBlock = PageBlock::first();
        $pageBlockSidebar1 = PageBlockSidebar::first();
        $pageBlockSidebar2 = PageBlockSidebar::find(2);
        $page = $pageBlock->page;

        Sanctum::actingAs($this->siteUser, ['*']);

        // Act
        $response = $this->patchJson(
            route('page.block.rel.sidebar.rel.reorder.update', [
                'page' => $page->id,
                'pageBlock' => $pageBlock->id,
                'pageBlockSidebar' => $pageBlockSidebar1->id,
            ]),
            ['direction' => 'down']
        );

        // Assert
        $response->assertOk()
            ->assertJson([
                'message' => 'Page block moved down',
            ]);

        // Verify that the orders have been updated in the database.
        $this->assertEquals(1, $pageBlockSidebar1->fresh()->order);
        $this->assertEquals(0, $pageBlockSidebar2->fresh()->order);
    }


    public function test_it_returns_404_if_page_does_not_exist(): void
    {
        // Arrange
        $pageBlock = PageBlock::factory()
        ->for(
            Page::factory()->create([
                'site_id' => $this->site->id,
            ]), 'page'
        )
        ->for(
            Block::factory()->create(), 'block'
        )
        ->has(
            Sidebar::factory()
            ->state([
                'site_id' => $this->site->id,
            ])
            ->count(2), 'sidebars'
        )
        ->create();
        $pageBlock = PageBlock::first();
        $pageBlockSidebar = PageBlockSidebar::first();
        $page = $pageBlock->page;
        Sanctum::actingAs($this->siteUser, ['*']);

        // Act
        $response = $this->patchJson(
            route('page.block.rel.sidebar.rel.reorder.update', [
                'page' => 999,
                'pageBlock' => $pageBlock->id,
                'pageBlockSidebar' => $pageBlockSidebar->id,
            ]),
            ['direction' => 'down']
        );

        // Assert
        $response->assertNotFound();
    }


    public function test_it_returns_404_if_page_block_does_not_exist(): void
    {
        // Arrange
        $pageBlock = PageBlock::factory()
        ->for(
            Page::factory()->create([
                'site_id' => $this->site->id,
            ]), 'page'
        )
        ->for(
            Block::factory()->create(), 'block'
        )
        ->has(
            Sidebar::factory()
            ->state([
                'site_id' => $this->site->id,
            ])
            ->count(2), 'sidebars'
        )
        ->create();
        $pageBlock = PageBlock::first();
        $pageBlockSidebar = PageBlockSidebar::first();
        $page = $pageBlock->page;

        Sanctum::actingAs($this->siteUser, ['*']);

        // Act
        $response = $this->patchJson(
            route('page.block.rel.sidebar.rel.reorder.update', [
                'page' => $page->id,
                'pageBlock' => 999,
                'pageBlockSidebar' => $pageBlockSidebar->id,
            ]),
            ['direction' => 'down']
        );

        // Assert
        $response->assertNotFound();
    }


    public function test_it_returns_404_if_page_block_sidebar_does_not_exist(): void
    {
        // Arrange
        $pageBlock = PageBlock::factory()
        ->for(
            Page::factory()->create([
                'site_id' => $this->site->id,
            ]), 'page'
        )
        ->for(
            Block::factory()->create(), 'block'
        )
        ->has(
            Sidebar::factory()
            ->state([
                'site_id' => $this->site->id,
            ])
            ->count(2), 'sidebars'
        )
        ->create();
        $pageBlock = PageBlock::first();
        $pageBlockSidebar = PageBlockSidebar::first();
        $page = $pageBlock->page;

        Sanctum::actingAs($this->siteUser, ['*']);

        // Act
        $response = $this->patchJson(
            route('page.block.rel.sidebar.rel.reorder.update', [
                'page' => $page->id,
                'pageBlock' => $pageBlock->id,
                'pageBlockSidebar' => 999,
            ]),
            ['direction' => 'down']
        );

        // Assert
        $response->assertNotFound();
    }


    public function test_it_requires_a_direction(): void
    {

        // Arrange
        $pageBlock = PageBlock::factory()
        ->for(
            Page::factory()->create([
                'site_id' => $this->site->id,
            ]), 'page'
        )
        ->for(
            Block::factory()->create(), 'block'
        )
        ->has(
            Sidebar::factory()
            ->state([
                'site_id' => $this->site->id,
            ])
            ->count(2), 'sidebars'
        )
        ->create();
        $pageBlock = PageBlock::first();
        $pageBlockSidebar = PageBlockSidebar::first();
        $page = $pageBlock->page;

        Sanctum::actingAs($this->siteUser, ['*']);

        // Act
        $response = $this->patchJson(
            route('page.block.rel.sidebar.rel.reorder.update', [
                'page' => $page->id,
                'pageBlock' => $pageBlock->id,
                'pageBlockSidebar' => $pageBlockSidebar->id,
            ]),
            []
        );

        // Assert
        $response->assertStatus(Response::HTTP_BAD_REQUEST);

        $response->assertExactJson([
            'message' => 'The request body cannot be empty for update operations. Please provide data to update.'
        ]);
    }


    public function test_it_requires_the_direction_to_be_a_valid_value(): void
    {
        // Arrange
        $pageBlock = PageBlock::factory()
        ->for(
            Page::factory()->create([
                'site_id' => $this->site->id,
            ]), 'page'
        )
        ->for(
            Block::factory()->create(), 'block'
        )
        ->has(
            Sidebar::factory()
            ->state([
                'site_id' => $this->site->id,
            ])
            ->count(2), 'sidebars'
        )
        ->create();
        $pageBlock = PageBlock::first();
        $pageBlockSidebar = PageBlockSidebar::first();
        $page = $pageBlock->page;


        Sanctum::actingAs($this->siteUser, ['*']);

        // Act
        $response = $this->patchJson(
            route('page.block.rel.sidebar.rel.reorder.update', [
                'page' => $page->id,
                'pageBlock' => $pageBlock->id,
                'pageBlockSidebar' => $pageBlockSidebar->id,
            ]),
            ['direction' => 'invalid']
        );

        // Assert
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['direction']);
    }
}
