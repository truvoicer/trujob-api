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
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PageBlockRoleControllerTest extends TestCase
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

    public function test_index_returns_roles_for_page_block()
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        $page = Page::factory()->create(['site_id' => $this->site->id]);
        $block = Block::factory()->create();
        $pageBlock = PageBlock::factory()->create([
            'page_id' => $page->id,
            'block_id' => $block->id,
        ]);
        $role1 = Role::factory()->create();
        $role2 = Role::factory()->create();

        $pageBlock->roles()->attach([$role1->id, $role2->id]);


        $response = $this
            ->getJson(route('page.block.rel.role.index', [$page->id, $pageBlock->id]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                ],
            ],
        ]);

        $response->assertJsonCount(2, 'data');

    }

    public function test_store_assigns_role_to_page_block()
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        $page = Page::factory()->create(['site_id' => $this->site->id]);
        $block = Block::factory()->create();
        $pageBlock = PageBlock::factory()->create([
            'page_id' => $page->id,
            'block_id' => $block->id,
        ]);
        $role = Role::factory()->create();


        $response = $this
            ->postJson(route('page.block.rel.role.store', [$page->id, $pageBlock->id, $role->id]));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Role assigned to page block.',
        ]);

        $this->assertDatabaseHas('page_block_role', [
            'page_block_id' => $pageBlock->id,
            'role_id' => $role->id,
        ]);
    }

    public function test_destroy_removes_role_from_page_block()
    {
        Sanctum::actingAs($this->siteUser, ['*']);

        $page = Page::factory()->create(['site_id' => $this->site->id]);
        $block = Block::factory()->create();
        $pageBlock = PageBlock::factory()->create([
            'page_id' => $page->id,
            'block_id' => $block->id,
        ]);
        $role = Role::factory()->create();

        $pageBlock->roles()->attach($role->id);


        $response = $this
            ->deleteJson(route('page.block.rel.role.destroy', [$page->id, $pageBlock->id, $role->id]));

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Role removed from page block.',
        ]);

        $this->assertDatabaseMissing('page_block_role', [
            'page_block_id' => $pageBlock->id,
            'role_id' => $role->id,
        ]);
    }
}