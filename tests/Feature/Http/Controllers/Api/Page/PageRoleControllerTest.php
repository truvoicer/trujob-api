<?php

namespace Tests\Feature\Api\Page;

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

class PageRoleControllerTest extends TestCase
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

    public function test_index_returns_roles_for_page()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $role = Role::factory()->create();
        $page->roles()->attach($role);

        Sanctum::actingAs($this->siteUser, ['*']);
        $this
            ->getJson(route('page.role.index', $page))
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'label',
                        'ability',
                    ],
                ],
            ]);
    }

    public function test_store_assigns_role_to_page()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $role = Role::factory()->create();

        Sanctum::actingAs($this->siteUser, ['*']);
        $this
            ->postJson(route('page.role.store', [$page, $role]))
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
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $role = Role::factory()->create();
        $page->roles()->attach($role);

        Sanctum::actingAs($this->siteUser, ['*']);
        $this
            ->deleteJson(route('page.role.destroy', [$page, $role]))
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
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);

        // Sanctum::actingAs($this->siteUser, ['*']);
        $this->getJson(route('page.role.index', $page))
            ->assertUnauthorized();
    }

    public function test_store_returns_401_when_unauthenticated()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $role = Role::factory()->create();

        // Sanctum::actingAs($user, ['*']);
        $this->postJson(route('page.role.store', [$page, $role]))
            ->assertUnauthorized();
    }

    public function test_destroy_returns_401_when_unauthenticated()
    {
        $user = User::factory()->create();
        $page = Page::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $role = Role::factory()->create();

        // Sanctum::actingAs($this->siteUser, ['api:app_user']);
        $this->deleteJson(route('page.role.destroy', [$page, $role]))
            ->assertUnauthorized();
    }
}