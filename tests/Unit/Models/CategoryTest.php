<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Discount;
use App\Models\Product;
use App\Models\ShippingRestriction;
use App\Models\ShippingZoneAble;
use App\Models\TaxRateAble;
use App\Models\Discountable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var Category
     */
    private $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->category = Category::factory()->create();
    }

    protected function tearDown(): void
    {
        unset($this->category);

        parent::tearDown();
    }

    public function testProductsRelationship(): void
    {
        $relation = $this->category->products();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertEquals('category_products', $relation->getTable());
        $this->assertEquals('category_id', $relation->getForeignPivotKeyName());
        $this->assertEquals('product_id', $relation->getRelatedPivotKeyName());
    }

    public function testDiscountsRelationship(): void
    {
        $relation = $this->category->discounts();

        $this->assertInstanceOf(BelongsToMany::class, $relation);
        $this->assertEquals('discount_categories', $relation->getTable());
        $this->assertEquals('category_id', $relation->getForeignPivotKeyName());
        $this->assertEquals('discount_id', $relation->getRelatedPivotKeyName());
    }

    public function testShippingRestrictionsRelationship(): void
    {
        $relation = $this->category->shippingRestrictions();

        $this->assertInstanceOf(MorphMany::class, $relation);
        $this->assertEquals('restrictionable_id', $relation->getForeignKeyName());
        $this->assertEquals('restrictionable_type', $relation->getMorphType());
    }

    public function testTaxRateAblesRelationship(): void
    {
        $relation = $this->category->taxRateAbles();

        $this->assertInstanceOf(MorphMany::class, $relation);
        $this->assertEquals('tax_rateable_id', $relation->getForeignKeyName());
        $this->assertEquals('tax_rateable_type', $relation->getMorphType());
    }

    public function testShippingZoneAblesRelationship(): void
    {
        $relation = $this->category->shippingZoneAbles();

        $this->assertInstanceOf(MorphMany::class, $relation);
        $this->assertEquals('shipping_zoneable_id', $relation->getForeignKeyName());
        $this->assertEquals('shipping_zoneable_type', $relation->getMorphType());
    }

    public function testDiscountablesRelationship(): void
    {
        $relation = $this->category->discountables();

        $this->assertInstanceOf(MorphMany::class, $relation);
        $this->assertEquals('discountable_id', $relation->getForeignKeyName());
        $this->assertEquals('discountable_type', $relation->getMorphType());
    }
}
