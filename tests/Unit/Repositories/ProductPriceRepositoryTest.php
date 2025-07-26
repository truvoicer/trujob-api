<?php

namespace Tests\Unit\Repositories;

use App\Models\Price;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\User;
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
        $user = User::factory()->create();
        // Create some ProductPrice records
        Product::factory()
            ->has(
                Price::factory()
                    ->count(2)
            )
            ->count(5)
            ->create([
                'user_id' => $user->id
            ]);

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
        $user = User::factory()->create();
        // Create some ProductPrice records
        Product::factory()
            ->has(
                Price::factory()
                    ->count(2)
            )
            ->count(5)
            ->create([
                'user_id' => $user->id,
            ]);

        $sort = 'id';
        $order = 'asc';

        $results = $this->productPriceRepository->findByParams($sort, $order);

        $this->assertCount(10, $results);
        $this->assertIsIterable($results);
        $this->assertInstanceOf(ProductPrice::class, $results->first());
    }

    public function testFindByQuery(): void
    {
        $user = User::factory()->create();
        // Create some ProductPrice records
        Product::factory()
            ->has(
                Price::factory()
                    ->count(2)
            )
            ->count(5)
            ->create([
                'user_id' => $user->id,
            ]);

        $results = $this->productPriceRepository->findByQuery('some_query');

        $this->assertCount(10, $results);
        $this->assertIsIterable($results);
        $this->assertInstanceOf(ProductPrice::class, $results->first());
    }
}
