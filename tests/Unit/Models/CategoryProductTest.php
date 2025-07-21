<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryProductTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->product = Product::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * Test the product relationship.
     *
     * @return void
     */
    public function testProductRelationship()
    {
        $categoryProduct = CategoryProduct::create([
            'category_id' => $this->category->id,
            'product_id' => $this->product->id,
        ]);

        $this->assertInstanceOf(Product::class, $categoryProduct->product);
        $this->assertEquals($this->product->id, $categoryProduct->product->id);
    }

    /**
     * Test the category relationship.
     *
     * @return void
     */
    public function testCategoryRelationship()
    {
        $categoryProduct = CategoryProduct::create([
            'category_id' => $this->category->id,
            'product_id' => $this->product->id,
        ]);

        $this->assertInstanceOf(Category::class, $categoryProduct->category);
        $this->assertEquals($this->category->id, $categoryProduct->category->id);
    }
}