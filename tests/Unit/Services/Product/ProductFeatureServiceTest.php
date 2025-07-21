<?php

namespace Tests\Unit\Services\Product;

use App\Models\Feature;
use App\Models\Product;
use App\Services\Product\ProductFeatureService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductFeatureServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductFeatureService $productFeatureService;
    protected Product $product;
    protected Feature $feature;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productFeatureService = new ProductFeatureService();
        $this->product = Product::factory()->create();
        $this->feature = Feature::factory()->create();
    }

    public function testAttachBulkFeaturesToProduct(): void
    {
        $features = Feature::factory(3)->create();
        $featureIds = $features->pluck('id')->toArray();

        $result = $this->productFeatureService->attachBulkFeaturesToProduct($this->product, $featureIds);

        $this->assertTrue($result);
        $this->assertCount(3, $this->product->features);
        foreach ($featureIds as $featureId) {
            $this->assertTrue($this->product->features()->where('feature_id', $featureId)->exists());
        }
    }

    public function testAttachFeatureToProduct(): void
    {
        $result = $this->productFeatureService->attachFeatureToProduct($this->product, $this->feature);

        $this->assertTrue($result);
        $this->assertCount(1, $this->product->features);
        $this->assertTrue($this->product->features()->where('feature_id', $this->feature->id)->exists());
    }

    public function testDetachFeatureFromProduct(): void
    {
        $this->product->features()->attach($this->feature->id);

        $result = $this->productFeatureService->detachFeatureFromProduct($this->product, $this->feature);

        $this->assertTrue($result);
        $this->assertCount(0, $this->product->features);
        $this->assertFalse($this->product->features()->where('feature_id', $this->feature->id)->exists());
    }

    public function testDetachFeatureFromProductThrowsExceptionWhenNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Product feature not found');

        $this->productFeatureService->detachFeatureFromProduct($this->product, $this->feature);
    }

    public function testDetachBulkFeaturesFromProduct(): void
    {
        $features = Feature::factory(3)->create();
        $featureIds = $features->pluck('id')->toArray();
        $this->product->features()->attach($featureIds);

        $result = $this->productFeatureService->detachBulkFeaturesFromProduct($this->product, $featureIds);

        $this->assertTrue($result);
        $this->assertCount(0, $this->product->features);
        foreach ($featureIds as $featureId) {
            $this->assertFalse($this->product->features()->where('feature_id', $featureId)->exists());
        }
    }
}
