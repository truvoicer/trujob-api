<?php

namespace Tests\Unit\Services\Auth;

use App\Enums\Auth\ApiAbility;
use App\Repositories\RoleRepository;
use App\Services\Auth\AuthService;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;
use Mockery;
use Mockery\MockInterface;

class AuthServiceTest extends TestCase
{
    /**
     * @var AuthService
     */
    private AuthService $authService;

    /**
     * @var MockInterface|RoleRepository
     */
    private MockInterface|RoleRepository $roleRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roleRepository = Mockery::mock(RoleRepository::class);
        $this->authService = new AuthService($this->roleRepository);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testFetchAvailableRoles(): void
    {
        // Arrange
        $roles = new Collection([
            (object)['name' => ApiAbility::SUPERUSER->value],
            (object)['name' => ApiAbility::ADMIN->value],
        ]);

        $expectedAvailableRolesNames = [
            ApiAbility::SUPERUSER->value,
            ApiAbility::ADMIN->value,
            ApiAbility::USER->value,
            ApiAbility::APP_USER->value,
        ];

        $expectedRoles = new Collection([
            (object)['id' => 1, 'name' => ApiAbility::SUPERUSER->value],
            (object)['id' => 2, 'name' => ApiAbility::ADMIN->value],
            (object)['id' => 3, 'name' => ApiAbility::USER->value],
            (object)['id' => 4, 'name' => ApiAbility::APP_USER->value],
        ]);

        $this->roleRepository
            ->shouldReceive('fetchRolesByNames')
            ->with($expectedAvailableRolesNames)
            ->once()
            ->andReturn($expectedRoles);

        // Act
        $result = $this->authService->fetchAvailableRoles($roles);

        // Assert
        $this->assertEquals($expectedRoles, $result);
    }

    public function testGetRoles(): void
    {
        // Arrange
        $roleIds = [1, 2];

        $fetchedRoles = new Collection([
            (object)['id' => 1, 'name' => ApiAbility::SUPERUSER->value],
            (object)['id' => 2, 'name' => ApiAbility::ADMIN->value],
        ]);

        $availableRoles = new Collection([
            (object)['id' => 1, 'name' => ApiAbility::SUPERUSER->value],
            (object)['id' => 2, 'name' => ApiAbility::ADMIN->value],
            (object)['id' => 3, 'name' => ApiAbility::USER->value],
            (object)['id' => 4, 'name' => ApiAbility::APP_USER->value],
        ]);

        $this->roleRepository
            ->shouldReceive('fetchRolesById')
            ->with($roleIds)
            ->once()
            ->andReturn($fetchedRoles);

        $this->roleRepository
            ->shouldReceive('fetchRolesByNames')
            ->with(Mockery::any())
            ->once()
            ->andReturn($availableRoles);

        // Act
        $result = $this->authService->getRoles($roleIds);

        // Assert
        $this->assertEquals($availableRoles, $result);
    }

    public function testGetRoleIds(): void
    {
        // Arrange
        $roleIds = [1, 2];

        $availableRoles = new Collection([
            (object)['id' => 1, 'name' => ApiAbility::SUPERUSER->value],
            (object)['id' => 2, 'name' => ApiAbility::ADMIN->value],
            (object)['id' => 3, 'name' => ApiAbility::USER->value],
            (object)['id' => 4, 'name' => ApiAbility::APP_USER->value],
        ]);

        $this->roleRepository
            ->shouldReceive('fetchRolesById')
            ->with($roleIds)
            ->once()
            ->andReturn(new Collection());

        $this->roleRepository
            ->shouldReceive('fetchRolesByNames')
            ->with(Mockery::any())
            ->once()
            ->andReturn($availableRoles);


        // Act
        $result = $this->authService->getRoleIds($roleIds);

        // Assert
        $this->assertEquals([1, 2, 3, 4], $result);
    }
}
