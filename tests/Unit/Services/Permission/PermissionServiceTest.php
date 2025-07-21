<?php

namespace Tests\Unit\Services\Permission;

use App\Models\Permission;
use App\Repositories\PermissionRepository;
use App\Services\Permission\PermissionService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tests\TestCase;

class PermissionServiceTest extends TestCase
{
    use RefreshDatabase;

    private PermissionService $permissionService;

    /** @var MockInterface|PermissionRepository */
    private MockInterface $permissionRepositoryMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock the PermissionRepository
        $this->permissionRepositoryMock = Mockery::mock(PermissionRepository::class);

        // Replace the injected PermissionRepository with our mock
        $this->app->bind(PermissionRepository::class, function () {
            return $this->permissionRepositoryMock;
        });

        $this->permissionService = new PermissionService();
        $this->permissionService->permissionRepository = $this->permissionRepositoryMock;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testFindByParams(): void
    {
        $sort = 'name';
        $order = 'asc';
        $count = 10;
        $permissions = [
            new Permission(['name' => 'test1', 'label' => 'Test 1']),
            new Permission(['name' => 'test2', 'label' => 'Test 2']),
        ];

        $this->permissionRepositoryMock->shouldReceive('setPagination')->once()->with(true);
        $this->permissionRepositoryMock->shouldReceive('setOrderByDir')->once()->with($order);
        $this->permissionRepositoryMock->shouldReceive('setOrderByColumn')->once()->with($sort);
        $this->permissionRepositoryMock->shouldReceive('setLimit')->once()->with($count);
        $this->permissionRepositoryMock->shouldReceive('findMany')->once()->andReturn($permissions);

        $result = $this->permissionService->findByParams($sort, $order, $count);

        $this->assertEquals($permissions, $result);
    }

    public function testGetPermissionById(): void
    {
        $permissionId = 1;
        $permission = new Permission(['id' => $permissionId, 'name' => 'test', 'label' => 'Test']);

        $this->permissionRepositoryMock->shouldReceive('findById')->once()->with($permissionId)->andReturn($permission);

        $result = $this->permissionService->getPermissionById($permissionId);

        $this->assertEquals($permission, $result);
    }

    public function testGetPermissionByIdNotFound(): void
    {
        $permissionId = 1;

        $this->permissionRepositoryMock->shouldReceive('findById')->once()->with($permissionId)->andReturn(null);

        $this->expectException(BadRequestHttpException::class);
        $this->expectExceptionMessage(sprintf("Permission id:%s not found in database.", $permissionId));

        $this->permissionService->getPermissionById($permissionId);
    }

    public function testCreatePermission(): void
    {
        $name = 'test';
        $label = 'Test';
        $permission = new Permission(['name' => $name, 'label' => $label]);

        $this->permissionRepositoryMock->shouldReceive('createPermission')->once()->with($name, $label)->andReturn($permission);

        $result = $this->permissionService->createPermission($name, $label);

        $this->assertEquals($permission, $result);
    }

    public function testUpdatePermission(): void
    {
        $permission = new Permission(['name' => 'old', 'label' => 'Old']);
        $data = ['name' => 'new', 'label' => 'New'];
        $updatedPermission = new Permission($data);

        $this->permissionRepositoryMock->shouldReceive('savePermission')->once()->with($permission, $data)->andReturn($updatedPermission);

        $result = $this->permissionService->updatePermission($permission, $data);

        $this->assertEquals($updatedPermission, $result);
    }

    public function testDeletePermission(): void
    {
        $permission = new Permission(['name' => 'test', 'label' => 'Test']);

        $this->permissionRepositoryMock->shouldReceive('setModel')->once()->with($permission);
        $this->permissionRepositoryMock->shouldReceive('delete')->once()->andReturn(true);

        $result = $this->permissionService->deletePermission($permission);

        $this->assertTrue($result);
    }

    public function testDeletePermissionById(): void
    {
        $permissionId = 1;
        $permission = new Permission(['id' => $permissionId, 'name' => 'test', 'label' => 'Test']);

        $this->permissionRepositoryMock->shouldReceive('findById')->once()->with($permissionId)->andReturn($permission);
        $this->permissionRepositoryMock->shouldReceive('setModel')->once()->with($permission);
        $this->permissionRepositoryMock->shouldReceive('delete')->once()->andReturn(true);

        $result = $this->permissionService->deletePermissionById($permissionId);

        $this->assertTrue($result);
    }

    public function testGetPermissionRepository(): void
    {
        $this->assertInstanceOf(PermissionRepository::class, $this->permissionService->getPermissionRepository());
    }
}