<?php

namespace Tests\Feature\Api;


use App\Enums\SiteStatus;
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
use App\Services\Permission\AccessControlService;
use Mockery;
use Mockery\MockInterface;

class AdminControllerTest extends TestCase
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
        Sanctum::actingAs($this->siteUser, ['*']);
    }

    public function test_get_user_role_list_requires_admin_role()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Mock the AccessControlService to simulate not being in admin group
        $this->mock(AccessControlService::class, function (MockInterface $mock) {
            $mock->shouldReceive('setUser')->andReturnSelf();
            $mock->shouldReceive('inAdminGroup')->andReturn(false);
        });

        // Act
        $response = $this->getJson(route('api.getUserRoleList'));

        // Assert
        $response->assertStatus(403)
            ->assertJson([
                'success' => false,
                'message' => "Access control: operation not permitted",
            ]);
    }

    public function test_get_user_role_list_returns_roles_when_admin()
    {
        // Arrange
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        // Mock the AccessControlService to simulate being in admin group
        $this->mock(AccessControlService::class, function (MockInterface $mock) {
            $mock->shouldReceive('setUser')->andReturnSelf();
            $mock->shouldReceive('inAdminGroup')->andReturn(true);
        });

        // Mock the UserAdminService to return a collection of roles (empty in this basic test)
        $roles = collect([]);  // Replace with actual roles if needed for more specific tests

        $this->app->bind(\App\Services\User\UserAdminService::class, function ($app) use ($roles) {
            $mock = Mockery::mock(\App\Services\User\UserAdminService::class);
            $mock->shouldReceive('findUserRoles')
                ->andReturn($roles);
            return $mock;
        });

        // Act
        $response = $this->getJson(route('api.getUserRoleList'));

        // Assert
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => "success",
                'data' => [], // Expected empty array based on our mocked roles
            ]);
    }

    protected function defineRoutePaths(): void
    {
        Route::prefix('api')
            ->group(function () {
                Route::get('/admin/roles', [\App\Http\Controllers\Api\AdminController::class, 'getUserRoleList'])->name('api.getUserRoleList');
            });
    }
}
