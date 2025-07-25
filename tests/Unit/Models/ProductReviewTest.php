<?php

namespace Tests\Unit\Models;

use App\Models\Product;
use App\Models\ProductReview;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductReviewTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the product relationship.
     *
     * @return void
     */
    public function testProductRelationship()
    {
        $user = User::factory()->create();
        // Create a product
        $product = Product::factory()->create([
            'user_id' => $user->id,
        ]);

        // Create a product review and associate it with the product
        $productReview = ProductReview::factory()->create(['product_id' => $product->id]);

        // Assert that the review's product relationship returns the correct product
        $this->assertInstanceOf(Product::class, $productReview->product);
        $this->assertEquals($product->id, $productReview->product->id);
    }
}
