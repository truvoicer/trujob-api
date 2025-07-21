<?php

namespace Tests\Unit\Services\Permission;

use App\Enums\Auth\ApiAbility;
use App\Models\User;
use App\Services\Auth\AuthService;
use App\Services\Permission\AccessControlService;
use App\Services\User\UserAdminService;
use Illuminate\Support\Facades\App;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class AccessControlServiceTest extends TestCase
{
    /**
     * @var AccessControlService
     */
    private AccessControlService $accessControlService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->accessControlService = App::make(AccessControlService::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test inAdminGroup returns true when user has SUPERUSER ability.
     *
     * @return void
     */
    public function testInAdminGroupReturnsTrueWhenUserHasSuperUserAbility(): void
    {
        // Arrange
        /** @var MockInterface $user */
        $user = Mockery::mock(User::class);

        $this->accessControlService->setUser($user);

        $this->mock(UserAdminService::class, function (MockInterface $mock) use ($user) {
            $mock->shouldReceive('userTokenHasAbility')
                ->once()
                ->with($user, ApiAbility::SUPERUSER)
                ->andReturn(true);
        });

        // Act
        $result = $this->accessControlService->inAdminGroup();

        // Assert
        $this->assertTrue($result);
    }

    /**
     * Test inAdminGroup returns false when user does not have SUPERUSER ability.
     *
     * @return void
     */
    public function testInAdminGroupReturnsFalseWhenUserDoesNotHaveSuperUserAbility(): void
    {
        // Arrange
        /** @var MockInterface $user */
        $user = Mockery::mock(User::class);

        $this->accessControlService->setUser($user);

        $this->mock(UserAdminService::class, function (MockInterface $mock) use ($user) {
            $mock->shouldReceive('userTokenHasAbility')
                ->once()
                ->with($user, ApiAbility::SUPERUSER)
                ->andReturn(false);
        });

        // Act
        $result = $this->accessControlService->inAdminGroup();

        // Assert
        $this->assertFalse($result);
    }
}
