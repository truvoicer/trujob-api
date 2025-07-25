<?php

namespace Tests\Feature\Api\Page\Block;

use App\Models\Page;
use App\Models\PageBlock;

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
    }

    public function test_it_can_reorder_a_page_block(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        $page = Page::factory()
        ->has(
            Block::factory()
                ->count(2),
        )
        ->create(['site_id' => $this->site->id]);
        $pageBlock1 = PageBlock::first();
        $pageBlock2 = PageBlock::find(2);
        $response = $this
            ->patchJson(route('page.block.rel.reorder.update', ['page' => $page->id, 'pageBlock' => $pageBlock1->id]), [
                'direction' => 'down',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Page block moved down',
            ]);

        $this->assertDatabaseHas('page_blocks', [
            'id' => $pageBlock1->id,
            'order' => 1,
        ]);

        $this->assertDatabaseHas('page_blocks', [
            'id' => $pageBlock2->id,
            'order' => 0,
        ]);


    }


    public function test_it_requires_a_valid_direction(): void
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        $page = Page::factory()
        ->has(
            Block::factory()
                ->count(2),
        )
        ->create(['site_id' => $this->site->id]);
        $pageBlock1 = PageBlock::first();
        $pageBlock2 = PageBlock::find(2);
        $response = $this
            ->patchJson(route('page.block.rel.reorder.update', ['page' => $page->id, 'pageBlock' => $pageBlock1->id]), [
                'direction' => 'invalid',
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['direction']);
    }


    public function test_it_returns_403_if_user_does_not_have_permission(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user, ['*']);

        $page = Page::factory()
        ->has(
            Block::factory()
                ->count(2),
        )
        ->create(['site_id' => $this->site->id]);
        $pageBlock1 = PageBlock::first();
        $pageBlock2 = PageBlock::find(2);
        $response = $this
            ->patchJson(route('page.block.rel.reorder.update', ['page' => $page->id, 'pageBlock' => $pageBlock1->id]), [
                'direction' => 'down',
            ]);
            // dd($response->getContent());
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }


}
