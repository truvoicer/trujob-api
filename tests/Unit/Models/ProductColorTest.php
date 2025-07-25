<?php

namespace Tests\Unit\Models;

use App\Models\Color;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductColorTest extends TestCase
{
    use RefreshDatabase;

    protected ProductColor $productColor;

    protected function setUp(): void
    {
        parent::setUp();
        $user = User::factory()->create();
        $color = Color::factory()->create();
        $product = Product::factory()->create(['user_id' => $user->id]);

        $this->productColor = ProductColor::factory()->create([
            'product_id' => $product->id,
            'color_id' => $color->id
        ]);
    }
    /**
     * Test the product relationship.
     *
     * @return void
     */
    public function testProductRelationship()
    {

        $this->assertInstanceOf(Product::class, $this->productColor->product);
    }

    /**
     * Test the color relationship.
     *
     * @return void
     */
    public function testColorRelationship()
    {
        $this->assertInstanceOf(Color::class, $this->productColor->color);
    }
}
