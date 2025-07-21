<?php

namespace Tests\Unit\Repositories;

use App\Models\Setting;
use App\Repositories\SettingRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private SettingRepository $settingRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->settingRepository = new SettingRepository();
    }

    public function testGetModelReturnsSettingInstance(): void
    {
        $model = $this->settingRepository->getModel();
        $this->assertInstanceOf(Setting::class, $model);
    }

    public function testFindByParamsReturnsCollectionOfSettings(): void
    {
        // Create some settings in the database
        Setting::factory()->count(3)->create();

        $settings = $this->settingRepository->findByParams('id', 'asc');

        $this->assertCount(3, $settings);
    }

    public function testFindByQueryParamsReturnsCollectionOfSettings(): void
    {
        // Create some settings in the database
        Setting::factory()->count(2)->create();

        $settings = $this->settingRepository->findByQuery([]);

        $this->assertCount(2, $settings);
    }
}
