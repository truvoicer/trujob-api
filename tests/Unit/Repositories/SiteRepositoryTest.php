<?php

namespace Tests\Unit\Repositories;

use App\Models\Site;
use App\Repositories\SiteRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private SiteRepository $siteRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->siteRepository = new SiteRepository();
    }

    public function testGetModel(): void
    {
        $model = $this->siteRepository->getModel();
        $this->assertInstanceOf(Site::class, $model);
    }

    public function testFindByParams(): void
    {
        // Arrange
        Site::factory()->count(3)->create();
        $sort = 'id';
        $order = 'asc';
        $count = 2;

        // Act
        $sites = $this->siteRepository->findByParams($sort, $order, $count);

        // Assert
        $this->assertCount($count, $sites);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $sites); // Assuming findAllWithParams returns a collection
        $this->assertEquals(1, $sites[0]->id);
        $this->assertEquals(2, $sites[1]->id);
    }

    public function testFindByQueryParams(): void
    {
        // Arrange
        Site::factory()->count(5)->create();

        // Act
        $sites = $this->siteRepository->findByQuery('some query'); // In this case query is not used

        // Assert
        $this->assertCount(5, $sites);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $sites);
    }

    protected function tearDown(): void
    {
        unset($this->siteRepository);
        parent::tearDown();
    }
}
