<?php

namespace Tests\Feature\Api\Page;

use App\Models\Page;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageRoleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_roles_for_page()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create();
        $role = Role::factory()->create();
        $page->roles()->attach($role);

        $this->actingAs($user)
            ->getJson(route('pages.roles.index', $page))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'created_at',
                        'updated_at',
                    ],
                ],
            ]);
    }

    public function test_store_assigns_role_to_page()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create();
        $role = Role::factory()->create();

        $this->actingAs($user)
            ->postJson(route('pages.roles.store', [$page, $role]))
            ->assertOk()
            ->assertJson([
                'message' => 'Role assigned to page.',
            ]);

        $this->assertDatabaseHas('page_role', [
            'page_id' => $page->id,
            'role_id' => $role->id,
        ]);
    }

    public function test_destroy_removes_role_from_page()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create();
        $role = Role::factory()->create();
        $page->roles()->attach($role);

        $this->actingAs($user)
            ->deleteJson(route('pages.roles.destroy', [$page, $role]))
            ->assertOk()
            ->assertJson([
                'message' => 'Role removed from page.',
            ]);

        $this->assertDatabaseMissing('page_role', [
            'page_id' => $page->id,
            'role_id' => $role->id,
        ]);
    }

     public function test_index_returns_401_when_unauthenticated()
    {
        $page = Page::factory()->create();

        $this->getJson(route('pages.roles.index', $page))
            ->assertUnauthorized();
    }

    public function test_store_returns_401_when_unauthenticated()
    {
        $page = Page::factory()->create();
        $role = Role::factory()->create();

        $this->postJson(route('pages.roles.store', [$page, $role]))
            ->assertUnauthorized();
    }

    public function test_destroy_returns_401_when_unauthenticated()
    {
        $page = Page::factory()->create();
        $role = Role::factory()->create();

        $this->deleteJson(route('pages.roles.destroy', [$page, $role]))
            ->assertUnauthorized();
    }
}