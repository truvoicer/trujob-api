<?php

namespace Tests\Unit\Repositories;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductBrand;
use App\Models\User;
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


    public function test_it_can_get_the_model()
    {
        $model = $this->productBrandRepository->getModel();

        $this->assertInstanceOf(ProductBrand::class, $model);
    }


    public function test_it_can_find_by_params()
    {
        // Arrange
        $user = User::factory()->create();
        Product::factory()
            ->has(
                Brand::factory()->count(3)
            )
            ->count(5)
            ->create([
                'user_id' => $user->id
            ]);

        $sort = 'id';
        $order = 'asc';
        $count = 2;

        // Act
        $result = $this->productBrandRepository->findByParams($sort, $order, $count);

        // Assert
        $this->assertCount($count, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }


    public function test_it_can_find_by_params_without_count()
    {
        // Arrange
        $user = User::factory()->create();
        Product::factory()
            ->has(
                Brand::factory()->count(3)
            )
            ->count(5)
            ->create([
                'user_id' => $user->id
            ]);
        $sort = 'id';
        $order = 'asc';

        // Act
        $result = $this->productBrandRepository->findByParams($sort, $order);

        // Assert
        $this->assertCount(15, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }


    public function test_it_can_find_by_query()
    {
        // Arrange
        $user = User::factory()->create();
        Product::factory()
            ->has(
                Brand::factory()->count(3)
            )
            ->count(5)
            ->create([
                'user_id' => $user->id
            ]);

        // Act
        $result = $this->productBrandRepository->findByQuery([]);

        // Assert
        $this->assertCount(15, $result);
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $result);
    }
}
