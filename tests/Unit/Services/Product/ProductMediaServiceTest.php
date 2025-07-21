<?php

namespace Tests\Unit\Services\Product;

use App\Models\Media;
use App\Models\Product;
use App\Services\Product\ProductMediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductMediaServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductMediaService $productMediaService;
    protected Product $product;
    protected Media $media1;
    protected Media $media2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productMediaService = new ProductMediaService();
        $this->product = Product::factory()->create();
        $this->media1 = Media::factory()->create();
        $this->media2 = Media::factory()->create();
    }

    public function testAttachBulkMediasToProduct(): void
    {
        $medias = [$this->media1->id, $this->media2->id];

        $result = $this->productMediaService->attachBulkMediasToProduct($this->product, $medias);

        $this->assertTrue($result);
        $this->assertCount(2, $this->product->media);
        $this->assertTrue($this->product->media()->where('media_id', $this->media1->id)->exists());
        $this->assertTrue($this->product->media()->where('media_id', $this->media2->id)->exists());
    }

    public function testAttachMediaToProduct(): void
    {
        $result = $this->productMediaService->attachMediaToProduct($this->product, $this->media1);

        $this->assertTrue($result);
        $this->assertCount(1, $this->product->media);
        $this->assertTrue($this->product->media()->where('media_id', $this->media1->id)->exists());
    }

    public function testDetachMediaFromProduct(): void
    {
        $this->product->media()->attach($this->media1->id);

        $result = $this->productMediaService->detachMediaFromProduct($this->product, $this->media1);

        $this->assertTrue($result);
        $this->assertCount(0, $this->product->media);
        $this->assertFalse($this->product->media()->where('media_id', $this->media1->id)->exists());
    }

    public function testDetachMediaFromProductThrowsExceptionIfNotFound(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Product media not found');

        $this->productMediaService->detachMediaFromProduct($this->product, $this->media1);
    }

    public function testDetachBulkMediasFromProduct(): void
    {
         $this->product->media()->attach([$this->media1->id, $this->media2->id]);

        $medias = [$this->media1->id, $this->media2->id];

        $result = $this->productMediaService->detachBulkMediasFromProduct($this->product, $medias);

        $this->assertTrue($result);
        $this->assertCount(0, $this->product->media);
        $this->assertFalse($this->product->media()->where('media_id', $this->media1->id)->exists());
        $this->assertFalse($this->product->media()->where('media_id', $this->media2->id)->exists());
    }
}