<?php

namespace Tests\Unit\Repositories;

use App\Models\Category;
use App\Repositories\CategoryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected CategoryRepository $categoryRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->categoryRepository = new CategoryRepository();
    }

    protected function tearDown(): void
    {
        unset($this->categoryRepository);
        parent::tearDown();
    }

    public function testGetModel(): void
    {
        $model = $this->categoryRepository->getModel();
        $this->assertInstanceOf(Category::class, $model);
    }

    public function testFindByParams(): void
    {
        // Create some categories for testing
        Category::factory()->count(3)->create();

        $categories = $this->categoryRepository->findByParams('name', 'asc');

        $this->assertCount(3, $categories);
    }

    public function testFindByQueryParams(): void
    {
         // Create some categories for testing
         Category::factory()->count(3)->create();

        $categories = $this->categoryRepository->findByQuery([]);
        $this->assertCount(3, $categories);

    }

}