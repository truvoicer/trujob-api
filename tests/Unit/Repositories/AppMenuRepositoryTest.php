<?php

namespace Tests\Unit\Repositories;

use App\Models\AppMenu;
use App\Repositories\AppMenuRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AppMenuRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var AppMenuRepository
     */
    private $appMenuRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->appMenuRepository = new AppMenuRepository();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->appMenuRepository);
    }

    public function testGetModel(): void
    {
        $model = $this->appMenuRepository->getModel();
        $this->assertInstanceOf(AppMenu::class, $model);
    }

    public function testFindByParams(): void
    {
        // Arrange
        AppMenu::factory()->count(3)->create();
        $sort = 'name';
        $order = 'asc';
        $count = 2;

        // Act
        $result = $this->appMenuRepository->findByParams($sort, $order, $count);

        // Assert
        $this->assertCount($count, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
        foreach ($result as $item) {
            $this->assertInstanceOf(AppMenu::class, $item);
        }
    }

    public function testFindByQueryParams(): void
    {
        // Arrange
        AppMenu::factory()->count(5)->create();
        $query = []; // In the original class, the query is ignored

        // Act
        $result = $this->appMenuRepository->findByQuery($query);

        // Assert
        $this->assertCount(5, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
        foreach ($result as $item) {
            $this->assertInstanceOf(AppMenu::class, $item);
        }
    }
}
