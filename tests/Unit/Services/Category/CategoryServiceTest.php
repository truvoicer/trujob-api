<?php

namespace Tests\Unit\Services\Category;

use App\Models\Category;
use App\Services\Category\CategoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CategoryService $categoryService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryService = new CategoryService();
    }

    
    public function test_it_can_create_a_category()
    {
        $data = [
            'name' => 'Test Category',
            'slug' => 'test-category',
        ];

        $result = $this->categoryService->createCategory($data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('categories', $data);
    }

    
    public function test_it_throws_an_exception_if_category_creation_fails()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error creating product category');

        // Simulate failure by providing invalid data (e.g., missing required fields)
        $data = [];
        $this->categoryService->createCategory($data);
    }

    
    public function test_it_can_update_a_category()
    {
        $category = Category::factory()->create();

        $data = [
            'name' => 'Updated Category Name',
            'slug' => 'updated-category-slug',
        ];

        $result = $this->categoryService->updateCategory($category, $data);

        $this->assertTrue($result);
        $this->assertDatabaseHas('categories', $data);
    }

    
    public function test_it_throws_an_exception_if_category_update_fails()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error updating product category');

        $category = Category::factory()->create();

        // Simulate failure by providing invalid data (e.g., violating unique constraint)
        $data = ['name' => null]; // causing validation error

        $this->categoryService->updateCategory($category, $data);
    }

    
    public function test_it_can_delete_a_category()
    {
        $category = Category::factory()->create();

        $result = $this->categoryService->deleteCategory($category);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    
    public function test_it_throws_an_exception_if_category_deletion_fails()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error deleting product category');

        $category = $this->getMockBuilder(Category::class)
            ->onlyMethods(['delete'])
            ->getMock();

        $category->method('delete')->willReturn(false);

        $this->categoryService->deleteCategory($category);
    }
}
