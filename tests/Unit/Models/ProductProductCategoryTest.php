<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductProductCategory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductProductCategoryTest extends TestCase
{
    use RefreshDatabase;

    protected ProductProductCategory $productProductCategory;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a new instance of the model for each test
        $this->productProductCategory = new ProductProductCategory();
    }

    
    public function test_it_has_fillable_attributes()
    {
        $expected = [
            'product_id',
            'product_category_id',
        ];

        $this->assertEquals($expected, $this->productProductCategory->getFillable());
    }

    
    public function test_it_has_table_name()
    {
        $expected = 'product_product_category';

        $this->assertEquals($expected, $this->productProductCategory->getTable());
    }


    
    public function test_it_can_define_a_relationship_to_product()
    {
        $relation = $this->productProductCategory->product();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('product_id', $relation->getForeignKeyName());
        $this->assertEquals((new Product())->getTable(), $relation->getRelated()->getTable());
    }

    
    public function test_it_can_define_a_relationship_to_product_category()
    {
        $relation = $this->productProductCategory->productCategory();

        $this->assertInstanceOf(BelongsTo::class, $relation);
        $this->assertEquals('product_category_id', $relation->getForeignKeyName());
        $this->assertEquals((new ProductCategory())->getTable(), $relation->getRelated()->getTable());
    }

    protected function tearDown(): void
    {
        unset($this->productProductCategory);

        parent::tearDown();
    }
}
