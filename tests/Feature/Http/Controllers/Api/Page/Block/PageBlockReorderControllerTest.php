<?php

namespace Tests\Feature\Api\Page\Block;

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

class PageBlockReorderControllerTest extends TestCase
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
    
    public function it_can_reorder_a_page_block(): void
    {
        $user = User::factory()->create();
        $page = Page::factory()->create(['site_id' => $user->site_id]);
        $pageBlock1 = PageBlock::factory()->create(['page_id' => $page->id, 'order' => 1]);
        $pageBlock2 = PageBlock::factory()->create(['page_id' => $page->id, 'order' => 2]);


        $response = $this
            ->postJson(route('page.blocks.reorder', ['page' => $page->id, 'page_block' => $pageBlock1->id]), [
                'direction' => 'down',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Page block moved down',
            ]);

        $this->assertDatabaseHas('page_blocks', [
            'id' => $pageBlock1->id,
            'order' => 2,
        ]);

        $this->assertDatabaseHas('page_blocks', [
            'id' => $pageBlock2->id,
            'order' => 1,
        ]);


    }

    
    public function it_requires_a_valid_direction(): void
    {
        $user = User::factory()->create();
        $page = Page::factory()->create(['site_id' => $user->site_id]);
        $pageBlock = PageBlock::factory()->create(['page_id' => $page->id]);

        $response = $this
            ->postJson(route('page.blocks.reorder', ['page' => $page->id, 'page_block' => $pageBlock->id]), [
                'direction' => 'invalid',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['direction']);
    }

    
    public function it_returns_403_if_user_does_not_have_permission(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $page = Page::factory()->create(['site_id' => $otherUser->site_id]);
        $pageBlock = PageBlock::factory()->create(['page_id' => $page->id]);

        $response = $this
            ->postJson(route('page.blocks.reorder', ['page' => $page->id, 'page_block' => $pageBlock->id]), [
                'direction' => 'down',
            ]);

        $response->assertStatus(403);
    }


}