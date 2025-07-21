<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use App\Services\Permission\AccessControlService;
use Mockery;
use Mockery\MockInterface;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

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