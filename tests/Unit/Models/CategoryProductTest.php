<?php

namespace Tests\Unit\Models;

use App\Models\CategoryProduct;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryProductTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var CategoryProduct
     */
    private $categoryProduct;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a CategoryProduct instance for testing
        $this->categoryProduct = CategoryProduct::factory()->create();
    }

    /**
     * Test the product relationship.
     *
     * @return void
     */
    public function testProductRelationship()
    {
        $product = Product::factory()->create();
        $this->categoryProduct->product_id = $product->id;
        $this->categoryProduct->save();

        $this->assertInstanceOf(Product::class, $this->categoryProduct->product);
        $this->assertEquals($product->id, $this->categoryProduct->product->id);
    }

    /**
     * Test the category relationship.
     *
     * @return void
     */
    public function testCategoryRelationship()
    {
        $category = ProductCategory::factory()->create();
        $this->categoryProduct->category_id = $category->id;
        $this->categoryProduct->save();

        $this->assertInstanceOf(ProductCategory::class, $this->categoryProduct->category);
        $this->assertEquals($category->id, $this->categoryProduct->category->id);
    }
}