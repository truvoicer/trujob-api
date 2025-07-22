<?php

namespace Tests\Feature;

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

class SidebarRoleControllerTest extends TestCase
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
    
    public function testIndexReturnsRolesForSidebar(): void
    {
        $user = User::factory()->create();
        $sidebar = Sidebar::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $role1 = Role::factory()->create();
        $role2 = Role::factory()->create();

        $sidebar->roles()->attach([$role1->id, $role2->id]);

        $response = $this
            ->getJson(route('sidebar.role.index', $sidebar));

        $response->assertStatus(200)
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

        $this->assertCount(2, $response->json('data'));
    }

    public function testStoreAssignsRoleToSidebar(): void
    {
        $sidebar = Sidebar::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $role = Role::factory()->create();

        $response = $this
            ->postJson(route('sidebar.role.store', ['sidebar' => $sidebar, 'role' => $role]));

        $response->assertStatus(200)
            ->assertJson([
                'message' => "Role assigned to sidebar.",
            ]);

        $this->assertDatabaseHas('sidebar_role', [
            'sidebar_id' => $sidebar->id,
            'role_id' => $role->id,
        ]);
    }

    public function testDestroyRemovesRoleFromSidebar(): void
    {
        $user = User::factory()->create();
        $sidebar = Sidebar::factory()->create([
            'site_id' => $this->site->id,
        ]);
        $role = Role::factory()->create();

        $sidebar->roles()->attach($role->id);

        $this->assertDatabaseHas('sidebar_role', [
            'sidebar_id' => $sidebar->id,
            'role_id' => $role->id,
        ]);

        $response = $this
            ->deleteJson(route('sidebar.role.destroy', ['sidebar' => $sidebar, 'role' => $role]));

        $response->assertStatus(200)
            ->assertJson([
                'message' => "Role removed from sidebar.",
            ]);

        $this->assertDatabaseMissing('sidebar_role', [
            'sidebar_id' => $sidebar->id,
            'role_id' => $role->id,
        ]);
    }
}