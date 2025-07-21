<?php

namespace Tests\Unit\Repositories;

use App\Models\UserSetting;
use App\Repositories\UserSettingRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserSettingRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserSettingRepository $userSettingRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userSettingRepository = new UserSettingRepository();
    }

    public function testFindByParams(): void
    {
        // Create some UserSetting records for testing
        UserSetting::factory()->count(3)->create();

        // Test sorting and ordering
        $result = $this->userSettingRepository->findByParams('id', 'asc');
        $this->assertCount(3, $result);
        $this->assertEquals(1, $result->first()->id);

        $result = $this->userSettingRepository->findByParams('id', 'desc');
        $this->assertCount(3, $result);
        $this->assertEquals(3, $result->first()->id);

        // Test count parameter
        $result = $this->userSettingRepository->findByParams('id', 'asc', 2);
        $this->assertCount(2, $result);
    }

    public function testGetModel(): void
    {
        $model = $this->userSettingRepository->getModel();
        $this->assertInstanceOf(UserSetting::class, $model);
    }
}
