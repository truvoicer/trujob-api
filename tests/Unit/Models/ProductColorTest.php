<?php

namespace Tests\Unit\Models;

use App\Models\Color;
use App\Models\Product;
use App\Models\ProductColor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductColorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the product relationship.
     *
     * @return void
     */
    public function testProductRelationship()
    {
        $productColor = ProductColor::factory()->create();

        $this->assertInstanceOf(Product::class, $productColor->product);
    }

    /**
     * Test the color relationship.
     *
     * @return void
     */
    public function testColorRelationship()
    {
        $productColor = ProductColor::factory()->create();

        $this->assertInstanceOf(Color::class, $productColor->color);
    }
}