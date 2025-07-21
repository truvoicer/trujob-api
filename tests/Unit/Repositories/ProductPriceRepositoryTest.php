<?php

namespace Tests\Unit\Repositories;

use App\Models\ProductPrice;
use App\Repositories\ProductPriceRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPriceRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ProductPriceRepository $productPriceRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productPriceRepository = new ProductPriceRepository();
    }

    protected function tearDown(): void
    {
        unset($this->productPriceRepository);
        parent::tearDown();
    }

    public function testGetModel(): void
    {
        $model = $this->productPriceRepository->getModel();
        $this->assertInstanceOf(ProductPrice::class, $model);
    }

    public function testFindByParams(): void
    {
        // Create some ProductPrice records
        ProductPrice::factory()->count(3)->create();

        $sort = 'id';
        $order = 'asc';
        $count = 2;

        $results = $this->productPriceRepository->findByParams($sort, $order, $count);

        $this->assertCount($count, $results);
        $this->assertIsIterable($results);
        $this->assertInstanceOf(ProductPrice::class, $results->first());
    }

    public function testFindByParamsWithoutCount(): void
    {
        // Create some ProductPrice records
        ProductPrice::factory()->count(5)->create();

        $sort = 'id';
        $order = 'asc';

        $results = $this->productPriceRepository->findByParams($sort, $order);

        $this->assertCount(5, $results);
        $this->assertIsIterable($results);
        $this->assertInstanceOf(ProductPrice::class, $results->first());
    }

    public function testFindByQuery(): void
    {
        // Create some ProductPrice records
        ProductPrice::factory()->count(4)->create();

        $results = $this->productPriceRepository->findByQuery('some_query');

        $this->assertCount(4, $results);
        $this->assertIsIterable($results);
        $this->assertInstanceOf(ProductPrice::class, $results->first());
    }
}
