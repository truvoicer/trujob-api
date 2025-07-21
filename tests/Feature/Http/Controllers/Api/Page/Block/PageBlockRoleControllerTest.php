<?php

namespace Tests\Feature\Api\Page\Block;

use App\Models\Page;
use App\Models\PageBlock;
use App\Models\Role;
use App\Models\User;
use App\Models\Site;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PageBlockRoleControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_index_returns_roles_for_page_block()
    {
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);
        $page = Page::factory()->create(['site_id' => $site->id]);
        $pageBlock = PageBlock::factory()->create(['page_id' => $page->id]);
        $role1 = Role::factory()->create();
        $role2 = Role::factory()->create();

        $pageBlock->roles()->attach([$role1->id, $role2->id]);

        $user->site_id = $site->id;
        $user->user_id = $user->id;
        $user->save();

        $response = $this->actingAs($user, 'api')
            ->getJson(route('api.pages.blocks.roles.index', [$page->id, $pageBlock->id]));

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
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);
        $page = Page::factory()->create(['site_id' => $site->id]);
        $pageBlock = PageBlock::factory()->create(['page_id' => $page->id]);
        $role = Role::factory()->create();

        $user->site_id = $site->id;
        $user->user_id = $user->id;
        $user->save();


        $response = $this->actingAs($user, 'api')
            ->postJson(route('api.pages.blocks.roles.store', [$page->id, $pageBlock->id, $role->id]));

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
        $user = User::factory()->create();
        $site = Site::factory()->create(['user_id' => $user->id]);
        $page = Page::factory()->create(['site_id' => $site->id]);
        $pageBlock = PageBlock::factory()->create(['page_id' => $page->id]);
        $role = Role::factory()->create();

        $pageBlock->roles()->attach($role->id);

        $user->site_id = $site->id;
        $user->user_id = $user->id;
        $user->save();

        $response = $this->actingAs($user, 'api')
            ->deleteJson(route('api.pages.blocks.roles.destroy', [$page->id, $pageBlock->id, $role->id]));

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