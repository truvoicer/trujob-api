<?php

namespace Tests\Unit\Services\Product;

use App\Models\Product;
use App\Models\ProductReview;
use App\Services\Product\ProductReviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductReviewServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductReviewService $productReviewService;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productReviewService = new ProductReviewService();
        $this->product = Product::factory()->create();
    }

    public function testAttachBulkReviewsToProduct(): void
    {
        $productReviewsData = [
            ['rating' => 5, 'comment' => 'Excellent product'],
            ['rating' => 4, 'comment' => 'Good product'],
        ];

        $result = $this->productReviewService->attachBulkReviewsToProduct($this->product, $productReviewsData);

        $this->assertTrue($result);
        $this->assertCount(2, $this->product->productReview);
        $this->assertEquals(5, $this->product->productReview[0]->rating);
        $this->assertEquals('Excellent product', $this->product->productReview[0]->comment);
    }

    public function testDetachBulkReviewsFromProduct(): void
    {
        $productReview1 = ProductReview::factory()->create(['product_id' => $this->product->id]);
        $productReview2 = ProductReview::factory()->create(['product_id' => $this->product->id]);

        $result = $this->productReviewService->detachBulkReviewsFromProduct($this->product, [$productReview1->id, $productReview2->id]);

        $this->assertTrue($result);
        $this->assertCount(0, $this->product->productReview);
    }

    public function testCreateProductReview(): void
    {
        $data = ['rating' => 3, 'comment' => 'Average product'];

        $result = $this->productReviewService->createProductReview($this->product, $data);

        $this->assertTrue($result);
        $this->assertCount(1, $this->product->productReview);
        $this->assertEquals(3, $this->product->productReview[0]->rating);
        $this->assertEquals('Average product', $this->product->productReview[0]->comment);
    }

    public function testCreateProductReviewThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error creating product review');

        $data = ['rating' => 3, 'comment' => 'Average product'];

        // Mocking the product review model to return false on save
        $mockProductReview = $this->getMockBuilder(ProductReview::class)
            ->onlyMethods(['save'])
            ->getMock();

        $mockProductReview->method('save')->willReturn(false);

        $this->productReviewService->createProductReview($this->product, $data);
    }

    public function testUpdateProductReview(): void
    {
        $productReview = ProductReview::factory()->create(['product_id' => $this->product->id]);
        $data = ['rating' => 2, 'comment' => 'Bad product'];

        $result = $this->productReviewService->updateProductReview($productReview, $data);

        $this->assertTrue($result);
        $this->assertEquals(2, $productReview->fresh()->rating);
        $this->assertEquals('Bad product', $productReview->fresh()->comment);
    }

    public function testUpdateProductReviewThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error updating product review');

        $productReview = ProductReview::factory()->create(['product_id' => $this->product->id]);
        $data = ['rating' => 2, 'comment' => 'Bad product'];

         // Mocking the product review model to return false on update
         $mockProductReview = $this->getMockBuilder(ProductReview::class)
         ->onlyMethods(['update'])
         ->getMock();

         $mockProductReview->method('update')->willReturn(false);
        app()->instance(ProductReview::class, $mockProductReview);

        $this->productReviewService->updateProductReview($productReview, $data);
    }


    public function testDeleteProductReview(): void
    {
        $productReview = ProductReview::factory()->create(['product_id' => $this->product->id]);

        $result = $this->productReviewService->deleteProductReview($productReview);

        $this->assertTrue($result);
        $this->assertNull(ProductReview::find($productReview->id));
    }

    public function testDeleteProductReviewThrowsException(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Error deleting product review');

        $productReview = ProductReview::factory()->create(['product_id' => $this->product->id]);

         // Mocking the product review model to return false on delete
         $mockProductReview = $this->getMockBuilder(ProductReview::class)
         ->onlyMethods(['delete'])
         ->getMock();

         $mockProductReview->method('delete')->willReturn(false);
        app()->instance(ProductReview::class, $mockProductReview);

        $this->productReviewService->deleteProductReview($productReview);
    }
}
