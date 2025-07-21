<?php

namespace Tests\Unit\Repositories;

use App\Models\AppMenuItem;
use App\Repositories\AppMenuItemRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppMenuItemRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected AppMenuItemRepository $appMenuItemRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->appMenuItemRepository = new AppMenuItemRepository();
    }

    public function tearDown(): void
    {
        unset($this->appMenuItemRepository);
        parent::tearDown();
    }

    public function testGetModelReturnsInstanceOfAppMenuItem(): void
    {
        $model = $this->appMenuItemRepository->getModel();
        $this->assertInstanceOf(AppMenuItem::class, $model);
    }

    public function testFindByParamsReturnsCollection(): void
    {
        // Create some AppMenuItem records in the database
        AppMenuItem::factory()->count(3)->create();

        $result = $this->appMenuItemRepository->findByParams('id', 'asc');

        $this->assertCount(3, $result);
    }

    public function testFindByQueryParamsReturnsCollection(): void
    {
        // Create some AppMenuItem records in the database
        AppMenuItem::factory()->count(2)->create();

        $result = $this->appMenuItemRepository->findByQuery(AppMenuItem::query());

        $this->assertCount(2, $result);
    }
}