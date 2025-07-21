<?php

namespace Tests\Unit\Services\Product;

use App\Models\Color;
use App\Models\Product;
use App\Services\Product\ProductColorService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductColorServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductColorService $productColorService;
    protected Product $product;
    protected Color $color;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productColorService = new ProductColorService();
        $this->product = Product::factory()->create();
        $this->color = Color::factory()->create();
    }

    public function testAttachBulkColorsToProduct(): void
    {
        $colors = Color::factory(3)->create();
        $colorIds = $colors->pluck('id')->toArray();

        $result = $this->productColorService->attachBulkColorsToProduct($this->product, $colorIds);

        $this->assertTrue($result);
        $this->assertCount(3, $this->product->colors);
        foreach ($colorIds as $colorId) {
            $this->assertDatabaseHas('color_product', [
                'product_id' => $this->product->id,
                'color_id' => $colorId,
            ]);
        }
    }

    public function testAttachColorToProduct(): void
    {
        $result = $this->productColorService->attachColorToProduct($this->product, $this->color);

        $this->assertTrue($result);
        $this->assertCount(1, $this->product->colors);
        $this->assertDatabaseHas('color_product', [
            'product_id' => $this->product->id,
            'color_id' => $this->color->id,
        ]);
    }

    public function testDetachColorFromProduct(): void
    {
        $this->product->colors()->attach($this->color->id);

        $result = $this->productColorService->detachColorFromProduct($this->product, $this->color);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('color_product', [
            'product_id' => $this->product->id,
            'color_id' => $this->color->id,
        ]);
    }

    public function testDetachColorFromProductThrowsExceptionIfNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Product color not found');

        $this->productColorService->detachColorFromProduct($this->product, $this->color);
    }

    public function testDetachBulkColorsFromProduct(): void
    {
        $colors = Color::factory(3)->create();
        $colorIds = $colors->pluck('id')->toArray();
        $this->product->colors()->attach($colorIds);

        $result = $this->productColorService->detachBulkColorsFromProduct($this->product, $colorIds);

        $this->assertTrue($result);
        foreach ($colorIds as $colorId) {
            $this->assertDatabaseMissing('color_product', [
                'product_id' => $this->product->id,
                'color_id' => $colorId,
            ]);
        }
    }
}
