<?php

namespace Tests\Unit\Repositories;

use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductRepositoryTest extends TestCase
{
    use RefreshDatabase;

    private ProductRepository $productRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productRepository = new ProductRepository();
    }

    public function testGetModelReturnsProductModel(): void
    {
        $model = $this->productRepository->getModel();

        $this->assertInstanceOf(Product::class, $model);
    }

    public function testFindByParamsReturnsCollectionOfProducts(): void
    {
        // Arrange
        Product::factory()->count(3)->create();

        // Act
        $products = $this->productRepository->findByParams('name', 'asc');

        // Assert
        $this->assertInstanceOf(Collection::class, $products);
        $this->assertCount(3, $products);
        $this->assertInstanceOf(Product::class, $products->first());
    }

    public function testFindByQueryParamsReturnsCollectionOfProducts(): void
    {
        // Arrange
        Product::factory()->count(2)->create();

        // Act
        $products = $this->productRepository->findByQuery('test');

        // Assert
        $this->assertInstanceOf(Collection::class, $products);
        $this->assertCount(2, $products);
        $this->assertInstanceOf(Product::class, $products->first());
    }
}