<?php

namespace Tests\Unit\Repositories;

use App\Models\RoleUser;
use App\Repositories\RoleUserRepository;
use Tests\TestCase;

class RoleUserRepositoryTest extends TestCase
{
    /**
     * @var RoleUserRepository
     */
    private $roleUserRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->roleUserRepository = new RoleUserRepository();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->roleUserRepository);
    }

    /**
     * @test
     */
    public function it_can_get_model()
    {
        $model = $this->roleUserRepository->getModel();

        $this->assertInstanceOf(RoleUser::class, $model);
    }
}