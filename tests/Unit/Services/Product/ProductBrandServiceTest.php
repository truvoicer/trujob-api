<?php

namespace Tests\Unit\Services\Product;

use App\Models\Brand;
use App\Models\Product;
use App\Services\Product\ProductBrandService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductBrandServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductBrandService $productBrandService;
    protected Product $product;
    protected Brand $brand;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productBrandService = new ProductBrandService();
        $this->product = Product::factory()->create();
        $this->brand = Brand::factory()->create();
    }

    public function testAttachBulkBrandsToProduct(): void
    {
        $brands = Brand::factory()->count(3)->create()->pluck('id')->toArray();

        $result = $this->productBrandService->attachBulkBrandsToProduct($this->product, $brands);

        $this->assertTrue($result);
        $this->assertCount(3, $this->product->brands);
        $this->assertEquals(count($brands), $this->product->brands()->count());
    }

    public function testAttachBrandToProduct(): void
    {
        $result = $this->productBrandService->attachBrandToProduct($this->product, $this->brand);

        $this->assertTrue($result);
        $this->assertCount(1, $this->product->brands);
        $this->assertTrue($this->product->brands()->where('brand_id', $this->brand->id)->exists());
    }

    public function testDetachBrandFromProduct(): void
    {
        $this->product->brands()->attach($this->brand->id);

        $result = $this->productBrandService->detachBrandFromProduct($this->product, $this->brand);

        $this->assertTrue($result);
        $this->assertCount(0, $this->product->brands);
        $this->assertFalse($this->product->brands()->where('brand_id', $this->brand->id)->exists());
    }

    public function testDetachBrandFromProductThrowsExceptionWhenNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Product brand not found');

        $this->productBrandService->detachBrandFromProduct($this->product, $this->brand);
    }

    public function testDetachBulkBrandsFromProduct(): void
    {
        $brands = Brand::factory()->count(3)->create()->pluck('id')->toArray();
        $this->product->brands()->attach($brands);
        $this->product->brands()->attach($this->brand->id);

        $result = $this->productBrandService->detachBulkBrandsFromProduct($this->product, $brands);

        $this->assertTrue($result);
        $this->assertCount(1, $this->product->brands);
        $this->assertTrue($this->product->brands()->where('brand_id', $this->brand->id)->exists());
    }
}