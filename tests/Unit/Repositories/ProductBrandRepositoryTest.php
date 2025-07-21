<?php

namespace Tests\Unit\Repositories;

use App\Models\ProductBrand;
use App\Repositories\ProductBrandRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductBrandRepositoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var ProductBrandRepository
     */
    private $productBrandRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productBrandRepository = new ProductBrandRepository();
    }

    protected function tearDown(): void
    {
        unset($this->productBrandRepository);
        parent::tearDown();
    }

    /** @test */
    public function it_can_get_the_model()
    {
        $model = $this->productBrandRepository->getModel();

        $this->assertInstanceOf(ProductBrand::class, $model);
    }

    /** @test */
    public function it_can_find_by_params()
    {
        // Arrange
        ProductBrand::factory()->count(3)->create();
        $sort = 'name';
        $order = 'asc';
        $count = 2;

        // Act
        $result = $this->productBrandRepository->findByParams($sort, $order, $count);

        // Assert
        $this->assertCount($count, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

     /** @test */
    public function it_can_find_by_params_without_count()
    {
        // Arrange
        ProductBrand::factory()->count(3)->create();
        $sort = 'name';
        $order = 'asc';

        // Act
        $result = $this->productBrandRepository->findByParams($sort, $order);

        // Assert
        $this->assertCount(3, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }

    /** @test */
    public function it_can_find_by_query()
    {
        // Arrange
        ProductBrand::factory()->count(5)->create();

        // Act
        $result = $this->productBrandRepository->findByQuery([]);

        // Assert
        $this->assertCount(5, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }
}